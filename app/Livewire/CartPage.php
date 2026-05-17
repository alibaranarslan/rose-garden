<?php

namespace App\Livewire;

use App\Models\CartItem;
use App\Models\Coupon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Canonical cart runtime owner after the `/sepet` route shell renders resources/views/cart/index.blade.php.
 */
class CartPage extends Component
{
    public $items;

    public string $couponCode = '';

    public ?string $couponMessage = null;

    public array $cardMessages = [];

    public function mount(): void
    {
        $this->refreshItems();
    }

    #[On('cart-updated')]
    public function refreshItems(): void
    {
        $this->items = $this->cartQuery()
            ->with(['product.images', 'variant'])
            ->get();
        $this->cardMessages = $this->items
            ->mapWithKeys(fn ($item) => [$item->id => (string) ($item->card_message ?? '')])
            ->all();
    }

    public function updateQuantity(int $itemId, int $qty): void
    {
        $item = $this->cartQuery()->whereKey($itemId)->firstOrFail();
        $item->update(['quantity' => max(1, $qty)]);
        $this->refreshItems();
        $this->dispatch('cart-updated');
    }

    public function removeItem(int $itemId): void
    {
        $this->cartQuery()->whereKey($itemId)->delete();
        $this->refreshItems();
        $this->dispatch('cart-updated');
    }

    public function saveCardMessage(int $itemId): void
    {
        $this->validate([
            'cardMessages.'.$itemId => ['nullable', 'string', 'max:500'],
        ]);

        $item = $this->cartQuery()->whereKey($itemId)->firstOrFail();
        $item->update([
            'card_message' => trim((string) ($this->cardMessages[$itemId] ?? '')),
        ]);

        $this->refreshItems();
        $this->dispatch('cart-updated');
        $this->dispatch('notify', type: 'success', message: __('Kart mesajı güncellendi.'));
    }

    public function applyCoupon(?string $code = null): void
    {
        $couponCode = strtoupper(trim((string) ($code ?? $this->couponCode)));
        $coupon = Coupon::active()->where('code', $couponCode)->first();

        if (! $coupon || ! $coupon->isValid($this->subtotal, auth()->id())) {
            session()->forget('cart_coupon_id');
            $this->couponMessage = __('Kupon geçersiz veya kullanılamaz.');

            return;
        }

        session(['cart_coupon_id' => $coupon->id]);
        $this->couponMessage = __('Kupon uygulandı.');
    }

    public function getSubtotalProperty(): float
    {
        return round((float) $this->items->sum(fn ($item) => $item->subtotal), 2);
    }

    public function getDiscountProperty(): float
    {
        $couponId = session('cart_coupon_id');
        if (! $couponId) {
            return 0;
        }

        $coupon = Coupon::find($couponId);
        if (! $coupon || ! $coupon->isValid($this->subtotal, auth()->id())) {
            return 0;
        }

        return $coupon->calculateDiscount($this->subtotal);
    }

    public function getTotalProperty(): float
    {
        return max(0, round($this->subtotal - $this->discount, 2));
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

    public function render()
    {
        return view('livewire.cart-page');
    }
}
