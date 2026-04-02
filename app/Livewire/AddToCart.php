<?php

namespace App\Livewire;

use App\Models\CartItem;
use App\Models\Product;
use Livewire\Component;

class AddToCart extends Component
{
    public int $productId;
    public ?int $variantId = null;
    public int $quantity = 1;
    public string $cardMessage = '';

    public function mount(int $productId): void
    {
        $this->productId = $productId;

        $product = Product::with('variants')->find($productId);
        if ($product && $product->variants->isNotEmpty()) {
            $this->variantId = $product->variants->first()->id;
        }
    }

    public function selectVariant(int $id): void
    {
        $this->variantId = $id;
    }

    public function incrementQty(): void
    {
        if ($this->quantity < 99) {
            $this->quantity++;
        }
    }

    public function decrementQty(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart(): void
    {
        $this->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'cardMessage' => ['nullable', 'string', 'max:500'],
        ]);

        $product = Product::find($this->productId);
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
        $this->dispatch('notify', type: 'success', message: __('Ürün sepete eklendi!'));
    }

    public function render()
    {
        $product = Product::with('variants')->find($this->productId);
        $variants = $product ? $product->variants->where('is_active', true) : collect();

        return view('livewire.add-to-cart', [
            'variants' => $variants,
            'product' => $product,
        ]);
    }
}
