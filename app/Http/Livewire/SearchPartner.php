<?php

namespace App\Http\Livewire;

use App\Models\Product;
use Livewire\Component;

class SearchPartner extends Component
{
      public $search;

    public $open = false;

    public function updatedSearch($value){
        if ($value) {
            $this->open = true;
        }else{
            $this->open = false;
        }
    }

    public function render()
    {

        if ($this->search) {
            $products = Product::where('name', 'LIKE' ,'%' . $this->search . '%')
            ->where('status', 2)
            ->where('quantity_partner', '>', 0)
            ->where('price_partner', '>', 0)
            ->take(5)
            ->get();

        } else {
            $products = [];
        }

        return view('livewire.search-partner', compact('products'));
    }
}
