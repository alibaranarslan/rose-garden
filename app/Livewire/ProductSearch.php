<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class ProductSearch extends Component
{
    public string $query = '';
    public $results;

    public function mount(): void
    {
        $this->results = collect();
    }

    public function updatedQuery(string $value): void
    {
        if (mb_strlen(trim($value)) < 2) {
            $this->results = collect();
            return;
        }

        $keyword = trim($value);
        $this->results = Product::active()
            ->with('images')
            ->where(function ($builder) use ($keyword) {
                $builder->whereRaw("JSON_EXTRACT(name, '$.tr') LIKE ?", ["%{$keyword}%"])
                    ->orWhereRaw("JSON_EXTRACT(short_description, '$.tr') LIKE ?", ["%{$keyword}%"]);
            })
            ->orderBy('sort_order')
            ->take(8)
            ->get();
    }

    public function render()
    {
        return view('livewire.product-search');
    }
}
