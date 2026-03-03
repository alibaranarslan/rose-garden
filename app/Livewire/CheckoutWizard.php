<?php

namespace App\Livewire;

use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\DeliveryTimeSlot;
use App\Models\DeliveryZone;
use App\Models\LoyaltyPoint;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Services\LoyaltyService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CheckoutWizard extends Component
{
    public int $step = 1;
    public ?string $senderName = null;
    public ?string $senderPhone = null;
    public ?string $senderEmail = null;
    public ?string $recipientName = null;
    public ?string $recipientPhone = null;
    public ?string $recipientAddress = null;
    public ?string $recipientDistrict = null;
    public ?string $deliveryDate = null;
    public ?int $deliveryTimeSlotId = null;
    public ?int $deliveryZoneId = null;
    public ?string $deliveryNote = null;
    public string $paymentMethod = 'credit_card';
    public bool $useLoyaltyPoints = false;
    public float $loyaltyPointsToUse = 0;
    public float $availableLoyaltyBalance = 0;
    public bool $distanceSalesAgreement = false;
    public bool $kvkkAcknowledgement = false;
    public bool $explicitConsent = false;
    public ?string $orderNumber = null;

    public function mount(): void
    {
        if (auth()->check()) {
            $this->senderName = auth()->user()->name;
            $this->senderPhone = auth()->user()->phone;
            $this->senderEmail = auth()->user()->email;
            $this->availableLoyaltyBalance = (float) optional(LoyaltyPoint::where('user_id', auth()->id())->first())->balance;
        }
    }

    public function nextStep(): void
    {
        $this->validateStep($this->step);
        $this->step = min(3, $this->step + 1);
    }

    public function prevStep(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    public function createOrder(): void
    {
        $this->validateStep(3);

        $items = $this->cartQuery()->with(['product', 'variant'])->get();
        if ($items->isEmpty()) {
            $this->addError('cart', __('Sepet boş olduğu için sipariş oluşturulamadı.'));
            return;
        }

        $outOfStock = $items->filter(fn ($item) => $item->product && $item->product->stock_status !== 'in_stock');
        if ($outOfStock->isNotEmpty()) {
            $names = $outOfStock->map(fn ($item) => $item->product->name)->join(', ');
            $this->addError('cart', __('Stokta olmayan ürünler: :names. Lütfen sepetinizi güncelleyiniz.', ['names' => $names]));
            return;
        }

        $subtotal = (float) $items->sum(fn ($item) => $item->subtotal);
        $deliveryZone = DeliveryZone::active()->findOrFail($this->deliveryZoneId);
        $deliveryFee = (float) $deliveryZone->calculateFee($subtotal);
        $coupon = Coupon::active()->find(session('cart_coupon_id'));
        $discountAmount = $coupon && $coupon->isValid($subtotal, auth()->id())
            ? (float) $coupon->calculateDiscount($subtotal)
            : 0.0;
        $loyaltyToUse = $this->resolveLoyaltyUsageAmount($subtotal, $deliveryFee, $discountAmount);
        $total = max(0, $subtotal + $deliveryFee - $discountAmount - $loyaltyToUse);

        $order = DB::transaction(function () use ($items, $subtotal, $deliveryFee, $discountAmount, $loyaltyToUse, $total, $coupon) {
            $order = Order::create([
                'user_id' => auth()->id(),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'discount_amount' => $discountAmount,
                'loyalty_points_used' => $loyaltyToUse,
                'total' => $total,
                'payment_method' => $this->paymentMethod,
                'sender_name' => $this->senderName,
                'sender_phone' => $this->senderPhone,
                'sender_email' => $this->senderEmail,
                'recipient_name' => $this->recipientName,
                'recipient_phone' => $this->recipientPhone,
                'recipient_address' => $this->recipientAddress,
                'recipient_district' => $this->recipientDistrict,
                'delivery_zone_id' => $this->deliveryZoneId,
                'delivery_date' => $this->deliveryDate,
                'delivery_time_slot_id' => $this->deliveryTimeSlotId,
                'delivery_note' => $this->deliveryNote,
                'coupon_id' => $coupon?->id,
                'ip_address' => request()->ip(),
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'product_name' => $item->product?->name ?? '',
                    'variant_name' => $item->variant?->name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->variant?->price ?? $item->product?->current_price ?? 0,
                    'total_price' => $item->subtotal,
                    'card_message' => $item->card_message,
                ]);
            }

            if ($coupon && $discountAmount > 0) {
                CouponUsage::create([
                    'coupon_id' => $coupon->id,
                    'user_id' => auth()->id(),
                    'order_id' => $order->id,
                    'discount_amount' => $discountAmount,
                    'created_at' => now(),
                ]);
                $coupon->increment('used_count');
            }

            if ($loyaltyToUse > 0 && auth()->check()) {
                app(LoyaltyService::class)->usePoints($order, $loyaltyToUse);
            }

            $this->cartQuery()->delete();
            session()->forget('cart_coupon_id');

            return $order;
        });

        $this->orderNumber = $order->order_number;
        $this->dispatch('cart-updated');

        if ($this->paymentMethod === 'credit_card') {
            $this->redirect(route('checkout.payment', ['order' => $order->id]));
            return;
        }

        session()->flash('order_success', true);
        session()->flash('payment_method', $this->paymentMethod);
        $this->redirect(route('checkout.success') . '?order=' . $order->order_number);
    }

    protected function validateStep(int $step): void
    {
        if ($step === 1) {
            $this->validate([
                'senderName' => ['required', 'string', 'max:255'],
                'senderPhone' => ['required', 'string', 'max:30'],
                'senderEmail' => ['required', 'email', 'max:255'],
                'recipientName' => ['required', 'string', 'max:255'],
                'recipientPhone' => ['required', 'string', 'max:30'],
                'recipientAddress' => ['required', 'string', 'max:500'],
                'recipientDistrict' => ['required', 'string', 'max:255'],
            ]);
        }

        if ($step === 2) {
            $this->validate([
                'deliveryDate' => ['required', 'date', 'after_or_equal:today'],
                'deliveryTimeSlotId' => ['required', 'integer', 'exists:delivery_time_slots,id'],
                'deliveryZoneId' => ['required', 'integer', 'exists:delivery_zones,id'],
            ]);
        }

        if ($step === 3) {
            $this->validate([
                'paymentMethod' => ['required', 'in:credit_card,bank_transfer,cash'],
                'useLoyaltyPoints' => ['boolean'],
                'loyaltyPointsToUse' => ['nullable', 'numeric', 'min:0'],
                'distanceSalesAgreement' => ['accepted'],
                'kvkkAcknowledgement' => ['accepted'],
                'explicitConsent' => ['accepted'],
            ]);

            if ($this->useLoyaltyPoints) {
                $minimumOrderForRedeem = (float) Setting::get('loyalty', 'min_order_amount', 0);
                $currentSubtotal = (float) $this->cartQuery()->with(['product', 'variant'])->get()->sum(fn ($item) => $item->subtotal);
                if ($minimumOrderForRedeem > 0 && $currentSubtotal < $minimumOrderForRedeem) {
                    $this->addError('loyaltyPointsToUse', __('Puan kullanımı için minimum sipariş tutarına ulaşılmadı.'));
                }

                if (!auth()->check()) {
                    $this->addError('loyaltyPointsToUse', __('Puan kullanımı için giriş yapmalısınız.'));
                }

                if ($this->loyaltyPointsToUse > $this->availableLoyaltyBalance) {
                    $this->addError('loyaltyPointsToUse', __('Kullanılacak puan bakiyeden fazla olamaz.'));
                }
            }
        }
    }

    private function resolveLoyaltyUsageAmount(float $subtotal, float $deliveryFee, float $discountAmount): float
    {
        if (!$this->useLoyaltyPoints || !auth()->check()) {
            return 0;
        }

        $minimumOrderForRedeem = (float) Setting::get('loyalty', 'min_order_amount', 0);
        if ($minimumOrderForRedeem > 0 && $subtotal < $minimumOrderForRedeem) {
            return 0;
        }

        $maxByBalance = max(0, $this->availableLoyaltyBalance);
        $maxByOrder = max(0, $subtotal + $deliveryFee - $discountAmount);
        $requested = max(0, (float) $this->loyaltyPointsToUse);

        return min($requested, $maxByBalance, $maxByOrder);
    }

    protected function cartQuery(): Builder
    {
        if (auth()->check()) {
            return CartItem::query()->where('user_id', auth()->id());
        }

        $sessionId = session('cart_session_id');
        if (!$sessionId) {
            $sessionId = session()->getId();
            session(['cart_session_id' => $sessionId]);
        }

        return CartItem::query()->where('session_id', $sessionId);
    }

    public function render()
    {
        return view('livewire.checkout-wizard', [
            'deliveryZones' => DeliveryZone::active()->get(),
            'deliveryTimeSlots' => DeliveryTimeSlot::active()->get(),
        ]);
    }
}
