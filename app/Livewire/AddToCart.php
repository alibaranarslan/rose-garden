<?php

namespace App\Livewire;

use App\Models\CartItem;
use Livewire\Component;

class AddToCart extends Component
{
    public int $productId;
    public ?int $variantId = null;
    public int $quantity = 1;
    public string $cardMessage = '';

    public function addToCart(): void
    {
        $this->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'cardMessage' => ['nullable', 'string', 'max:500'],
        ]);

        $product = \App\Models\Product::find($this->productId);
        if (!$product || $product->stock_status !== 'in_stock') {
            $this->dispatch('notify', type: 'error', message: 'Bu ürün şu anda stokta bulunmamaktadır.');
            return;
        }

        $payload = [
            'product_id' => $this->productId,
            'variant_id' => $this->variantId,
            'card_message' => trim($this->cardMessage),
        ];

        if (auth()->check()) {
            $payload['user_id'] = auth()->id();
        } else {
            $sessionId = session('cart_session_id');
            if (!$sessionId) {
                $sessionId = session()->getId();
                session(['cart_session_id' => $sessionId]);
            }
            $payload['session_id'] = $sessionId;
        }

        $item = CartItem::query()->where($payload)->first();

        if ($item) {
            $item->increment('quantity', $this->quantity);
        } else {
            CartItem::create($payload + ['quantity' => $this->quantity]);
        }

        $this->dispatch('cart-updated');
    }

    public function render()
    {
        return view('livewire.add-to-cart');
    }
}
