<?php

namespace App\Http\Livewire;

use Livewire\Component;

class CategoryPrincipal extends Component
{
    public $category;
    public $products = [];
    public $hasProducts = false;

    public function loadPosts()
    {
        $this->products = $this->category->products()
            ->where(function ($query) {
                $query->where('status', 2)
                      ->where('product_principal', 2);
            })
            ->take(18)
            ->get();

        $this->hasProducts = count($this->products) > 0;

        $this->emit('glider', $this->category->id);
    }

    public function render()
    {
        return view('livewire.category-principal');
    }
}
