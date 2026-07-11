<?php

namespace App\Livewire;

use App\Models\CartItem;
use App\Models\Product;
use Livewire\Component;

class AddToCart extends Component
{
    public int $productId;

    /** @var 'card'|'detail' */
    public string $layout = 'detail';

    public ?int $variantId = null;

    public int $quantity = 1;

    public string $cardMessage = '';

    public bool $showCartMessageModal = false;

    public bool $showAddedNotice = false;

    public function mount(int $productId, string $layout = 'detail'): void
    {
        $this->productId = $productId;
        $this->layout = in_array($layout, ['card', 'detail'], true) ? $layout : 'detail';

        $product = Product::with('variants')->find($productId);
        if ($product && $product->variants->where('is_active', true)->isNotEmpty()) {
            $this->variantId = $product->variants->where('is_active', true)->first()->id;
        }
    }

    public function updatedVariantId(mixed $value): void
    {
        $this->variantId = $value !== null && $value !== '' ? (int) $value : null;
        $this->showAddedNotice = false;
    }

    public function incrementQty(): void
    {
        if ($this->quantity < 99) {
            $this->quantity++;
            $this->showAddedNotice = false;
        }
    }

    public function decrementQty(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
            $this->showAddedNotice = false;
        }
    }

    public function openCartMessageModal(): void
    {
        if (! config('storefront.orders_enabled', true)) {
            $this->dispatch('notify', type: 'info', message: __('Online sipariş çok yakında açılacak.'));

            return;
        }

        $this->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $product = Product::with(['variants' => fn ($q) => $q->where('is_active', true)])->find($this->productId);
        if (! $product || $product->stock_status !== 'in_stock') {
            $this->dispatch('notify', type: 'error', message: __('Bu ürün şu anda stokta bulunmamaktadır.'));

            return;
        }

        $activeVariants = $product->variants;
        if ($activeVariants->isNotEmpty() && ! $this->variantId) {
            $this->dispatch('notify', type: 'error', message: __('Lütfen bir seçenek seçin.'));

            return;
        }

        $this->showCartMessageModal = true;
    }

    public function closeCartMessageModal(): void
    {
        $this->showCartMessageModal = false;
    }

    public function addToCart(): void
    {
        if (! config('storefront.orders_enabled', true)) {
            $this->dispatch('notify', type: 'info', message: __('Online sipariş çok yakında açılacak.'));
            $this->showCartMessageModal = false;

            return;
        }

        $this->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'cardMessage' => ['nullable', 'string', 'max:500'],
        ]);

        $product = Product::find($this->productId);
        if (! $product || $product->stock_status !== 'in_stock') {
            $this->dispatch('notify', type: 'error', message: __('Bu ürün şu anda stokta bulunmamaktadır.'));

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
            if (! $sessionId) {
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
        $this->showCartMessageModal = false;
        $this->showAddedNotice = true;
        $this->cardMessage = '';
    }

    public function render()
    {
        $product = Product::with('variants')->find($this->productId);
        $variants = $product ? $product->variants->where('is_active', true)->sortBy('sort_order') : collect();

        $selectedVariant = $this->variantId ? $variants->firstWhere('id', $this->variantId) : null;
        $priceAmount = 0.0;
        $compareAmount = null;
        if ($product) {
            if ($variants->isEmpty()) {
                $priceAmount = (float) ($product->sale_price ?? $product->price);
                $compareAmount = $product->sale_price ? (float) $product->price : null;
            } elseif ($selectedVariant) {
                $priceAmount = (float) ($selectedVariant->sale_price ?? $selectedVariant->price);
                $compareAmount = $selectedVariant->sale_price ? (float) $selectedVariant->price : null;
            }
        }

        return view('livewire.add-to-cart', [
            'variants' => $variants,
            'product' => $product,
            'priceAmount' => $priceAmount,
            'compareAmount' => $compareAmount,
            'ordersEnabled' => config('storefront.orders_enabled', true),
        ]);
    }
}
