<?php

namespace App\Livewire;

use App\Models\Favorite;
use App\Support\StorefrontLocale;
use Livewire\Component;

class FavoriteToggle extends Component
{
    public int $productId;

    public bool $isFavorited = false;

    public function mount(): void
    {
        if (! auth()->check()) {
            $this->isFavorited = false;

            return;
        }

        $this->isFavorited = Favorite::query()
            ->where('user_id', auth()->id())
            ->where('product_id', $this->productId)
            ->exists();
    }

    public function toggle(): void
    {
        if (! auth()->check()) {
            $this->redirect(StorefrontLocale::route('login'), navigate: true);

            return;
        }

        $favorite = Favorite::query()
            ->where('user_id', auth()->id())
            ->where('product_id', $this->productId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $this->isFavorited = false;

            return;
        }

        Favorite::create([
            'user_id' => auth()->id(),
            'product_id' => $this->productId,
            'created_at' => now(),
        ]);

        $this->isFavorited = true;
    }

    public function render()
    {
        return view('livewire.favorite-toggle');
    }
}
