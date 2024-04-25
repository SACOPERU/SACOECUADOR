<?php

namespace App\Http\Livewire\Partner;

use App\Models\Product;
use Livewire\Component;

class ShowProducts extends Component
{

    public $search;

    public function render()
    {

        $products = Product::paginate(12);

        return view('livewire.partner.show-products', compact('products'))->layout('layouts.partner');
    }
}
