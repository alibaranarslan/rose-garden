<?php

namespace App\Livewire;

use App\Models\CartItem;
use Livewire\Attributes\On;
use Livewire\Component;

class CartIcon extends Component
{
    public int $count = 0;

    public function mount(): void
    {
        $this->refreshCount();
    }

    #[On('cart-updated')]
    public function refreshCount(): void
    {
        $this->count = (int) $this->cartQuery()->sum('quantity');
    }

    protected function cartQuery()
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
        return view('livewire.cart-icon');
    }
}
