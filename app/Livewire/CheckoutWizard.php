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
use App\Support\PaymentSettings;
use App\Support\StorefrontLocale;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

/**
 * Canonical checkout-entry runtime owner after the `/odeme` route shell renders checkout.index.
 *
 * This component creates orders and then hands payment continuation to CheckoutController.
 */
class CheckoutWizard extends Component
{
    public int $step = 1;

    public ?int $savedAddressId = null;

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

    public bool $prefixLocaleRoutes = false;

    public function mount(): void
    {
        $this->prefixLocaleRoutes = request()->route('locale') !== null;

        if (auth()->check()) {
            $user = auth()->user();
            $defaultAddress = $user->addresses()->where('is_default', true)->first();

            $this->senderName = $user->name;
            $this->senderPhone = $user->phone;
            $this->senderEmail = $user->email;
            $this->availableLoyaltyBalance = (float) optional(LoyaltyPoint::where('user_id', auth()->id())->first())->balance;

            if ($defaultAddress) {
                $this->savedAddressId = $defaultAddress->id;
                $this->recipientName ??= $defaultAddress->recipient_name;
                $this->recipientPhone ??= $defaultAddress->recipient_phone;
                $this->recipientAddress ??= $defaultAddress->address_line;
                $this->recipientDistrict ??= $defaultAddress->district;
            }
        }

        if (! PaymentSettings::isPaytrConfigured()) {
            $this->paymentMethod = 'bank_transfer';
        }
    }

    public function updatedSavedAddressId(mixed $value): void
    {
        if (! auth()->check()) {
            return;
        }

        $address = auth()->user()
            ->addresses()
            ->whereKey((int) $value)
            ->first();

        if (! $address) {
            return;
        }

        $this->recipientName = $address->recipient_name;
        $this->recipientPhone = $address->recipient_phone;
        $this->recipientAddress = $address->address_line;
        $this->recipientDistrict = $address->district;
    }

    public function updated(string $property): void
    {
        $validatedFields = [
            'senderName',
            'senderPhone',
            'senderEmail',
            'recipientName',
            'recipientPhone',
            'recipientAddress',
            'recipientDistrict',
            'deliveryDate',
            'deliveryTimeSlotId',
            'deliveryZoneId',
            'paymentMethod',
            'loyaltyPointsToUse',
            'distanceSalesAgreement',
            'kvkkAcknowledgement',
            'explicitConsent',
        ];

        if (in_array($property, $validatedFields, true)) {
            $this->resetValidation($property);
        }

        if (in_array($property, ['deliveryDate', 'deliveryTimeSlotId', 'deliveryZoneId'], true)) {
            $this->resetValidation('deliveryConfiguration');
        }
    }

    public function nextStep(): void
    {
        if ($this->step === 2 && $this->deliveryConfigurationIsMissing()) {
            $this->addError(
                'deliveryConfiguration',
                __('Teslimat ilerleyemiyor. En az bir aktif teslimat bölgesi ve bir aktif saat aralığı tanımlanmalı.')
            );

            return;
        }

        $this->validateStep($this->step);
        $this->step = min(3, $this->step + 1);
    }

    public function prevStep(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    public function createOrder(): void
    {
        if ($this->deliveryConfigurationIsMissing()) {
            $this->addError(
                'deliveryConfiguration',
                __('Teslimat ayarları eksik. Sipariş oluşturmak için aktif teslimat bölgesi ve saat aralığı tanımlanmalı.')
            );

            return;
        }

        $this->validateStep(3);

        if ($this->paymentMethod === 'credit_card' && ! PaymentSettings::isPaytrConfigured()) {
            $this->addError('paymentMethod', __('Kart ile ödeme henüz aktif değil. Lütfen havale/EFT seçeneğini kullanın.'));

            return;
        }

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
        $deliveryZone = DeliveryZone::active()->find($this->deliveryZoneId);
        $deliveryTimeSlot = DeliveryTimeSlot::active()->find($this->deliveryTimeSlotId);

        if (! $deliveryZone) {
            $this->addError('deliveryZoneId', __('Seçilen teslimat bölgesi aktif değil ya da silinmiş.'));

            return;
        }

        if (! $deliveryTimeSlot) {
            $this->addError('deliveryTimeSlotId', __('Seçilen saat aralığı aktif değil ya da silinmiş.'));

            return;
        }

        $deliveryFee = (float) $deliveryZone->calculateFee($subtotal);
        $coupon = Coupon::active()->find(session('cart_coupon_id'));
        $discountAmount = $coupon && $coupon->isValid($subtotal, auth()->id())
            ? (float) $coupon->calculateDiscount($subtotal)
            : 0.0;

        // free_delivery coupon type resets delivery fee regardless of discount amount
        if ($coupon && $coupon->type === 'free_delivery') {
            $deliveryFee = 0.0;
        }

        $loyaltyToUse = $this->resolveLoyaltyUsageAmount($subtotal, $deliveryFee, $discountAmount);
        $total = max(0, $subtotal + $deliveryFee - $discountAmount - $loyaltyToUse);

        $order = DB::transaction(function () use ($items, $subtotal, $deliveryFee, $discountAmount, $loyaltyToUse, $total, $coupon) {
            $order = Order::createWithGeneratedNumber([
                'user_id' => auth()->id(),
                'status' => $this->paymentMethod === 'bank_transfer' ? 'awaiting_payment' : 'pending',
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
                $result = app(LoyaltyService::class)->usePoints($order, $loyaltyToUse);
                if ($result === false) {
                    // Points insufficient or invalid; reset to zero (order still valid)
                    $order->update(['loyalty_points_used' => 0, 'total' => $subtotal + $deliveryFee - $discountAmount]);
                }
            }

            $this->cartQuery()->delete();
            session()->forget('cart_coupon_id');

            return $order;
        });

        $this->orderNumber = $order->order_number;
        session(['last_order_number' => $order->order_number]);
        $this->dispatch('cart-updated');

        if ($this->paymentMethod === 'credit_card') {
            $this->redirect(StorefrontLocale::route('checkout.payment', ['order' => $order->id], null, true, $this->prefixLocaleRoutes));

            return;
        }

        session()->flash('order_success', true);
        session()->flash('payment_method', $this->paymentMethod);
        $this->redirect(StorefrontLocale::route('checkout.success', ['order' => $order->order_number], null, true, $this->prefixLocaleRoutes));
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
                'deliveryTimeSlotId' => ['required', 'integer', Rule::exists('delivery_time_slots', 'id')->where('is_active', true)],
                'deliveryZoneId' => ['required', 'integer', Rule::exists('delivery_zones', 'id')->where('is_active', true)],
            ]);
        }

        if ($step === 3) {
            $this->validate([
                'paymentMethod' => ['required', 'in:credit_card,bank_transfer'],
                'useLoyaltyPoints' => ['boolean'],
                'loyaltyPointsToUse' => ['nullable', 'numeric', 'min:0'],
                'distanceSalesAgreement' => ['accepted'],
                'kvkkAcknowledgement' => ['accepted'],
                'explicitConsent' => ['accepted'],
            ]);

            if ($this->useLoyaltyPoints) {
                $minimumOrderForRedeem = (float) Setting::get('loyalty', 'min_use_amount', 0);
                $currentSubtotal = (float) $this->cartQuery()->with(['product', 'variant'])->get()->sum(fn ($item) => $item->subtotal);
                if ($minimumOrderForRedeem > 0 && $currentSubtotal < $minimumOrderForRedeem) {
                    $this->addError('loyaltyPointsToUse', __('Puan kullanımı için minimum sipariş tutarına ulaşılmadı.'));
                }

                if (! auth()->check()) {
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
        if (! $this->useLoyaltyPoints || ! auth()->check()) {
            return 0;
        }

        $minimumOrderForRedeem = (float) Setting::get('loyalty', 'min_use_amount', 0);
        if ($minimumOrderForRedeem > 0 && $subtotal < $minimumOrderForRedeem) {
            return 0;
        }

        $maxByBalance = max(0, $this->availableLoyaltyBalance);
        $maxByOrder = max(0, $subtotal + $deliveryFee - $discountAmount);
        $requested = max(0, (float) $this->loyaltyPointsToUse);

        return min($requested, $maxByBalance, $maxByOrder);
    }

    private function estimateGuestLoyaltyPoints(): float
    {
        if (auth()->check()) {
            return 0.0;
        }

        $items = $this->cartQuery()->with(['product', 'variant'])->get();
        if ($items->isEmpty()) {
            return 0.0;
        }

        $subtotal = (float) $items->sum(fn ($item) => $item->subtotal);
        $deliveryFee = 0.0;
        $discountAmount = 0.0;

        if ($this->deliveryZoneId) {
            $deliveryZone = DeliveryZone::active()->find($this->deliveryZoneId);
            if ($deliveryZone) {
                $deliveryFee = (float) $deliveryZone->calculateFee($subtotal);
            }
        }

        $coupon = Coupon::active()->find(session('cart_coupon_id'));
        if ($coupon && $coupon->isValid($subtotal, null)) {
            $discountAmount = (float) $coupon->calculateDiscount($subtotal);

            if ($coupon->type === 'free_delivery') {
                $deliveryFee = 0.0;
            }
        }

        $baseAmount = max(0, $subtotal + $deliveryFee - $discountAmount);

        return app(LoyaltyService::class)->estimateEarnedPoints($baseAmount);
    }

    protected function cartQuery(): Builder
    {
        if (auth()->check()) {
            return CartItem::query()->where('user_id', auth()->id());
        }

        $sessionId = session('cart_session_id');
        if (! $sessionId) {
            $sessionId = session()->getId();
            session(['cart_session_id' => $sessionId]);
        }

        return CartItem::query()->where('session_id', $sessionId);
    }

    protected function deliveryConfigurationIsMissing(): bool
    {
        return ! DeliveryZone::active()->exists() || ! DeliveryTimeSlot::active()->exists();
    }

    public function render()
    {
        $now = now();
        $slots = DeliveryTimeSlot::active()->get();

        // Filter out slots that have passed their cutoff time for today's delivery
        if ($this->deliveryDate) {
            $selectedDate = Carbon::parse($this->deliveryDate);

            if ($selectedDate->isToday() && $this->deliveryZoneId) {
                $zone = DeliveryZone::find($this->deliveryZoneId);
                $cutoff = $zone?->cutoff_time ?? '20:00';

                $slots = $slots->filter(function ($slot) use ($now, $cutoff) {
                    return $now->format('H:i') < $cutoff
                        && $now->format('H:i') < $slot->start_time;
                });
            }
        }

        return view('livewire.checkout-wizard', [
            'deliveryZones' => DeliveryZone::active()->get(),
            'deliveryTimeSlots' => $slots,
            'bankInfo' => PaymentSettings::bankTransferDetails(),
            'paytrConfigured' => PaymentSettings::isPaytrConfigured(),
            'minimumLoyaltyOrderAmount' => (float) Setting::get('loyalty', 'min_use_amount', 0),
            'guestLoyaltyEstimate' => $this->estimateGuestLoyaltyPoints(),
            'savedAddresses' => auth()->check() ? auth()->user()->addresses()->latest()->get() : collect(),
        ]);
    }
}
