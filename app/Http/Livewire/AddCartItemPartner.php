<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Storage;

class AddCartItemPartner extends Component
{	
  
 public $product, $quantity_partner;
    public $qty = 1;
    public $options = [
        'color_id' => null,
        'size_id' => null,
        'sku'

    ];

    public function mount(){
        $this->quantity_partner = qty_available($this->product->id);
        $this->options['image'] = Storage::url($this->product->images->first()->url);
        $this->options['sku'] = $this->product->sku;

    }

    public function decrement(){
        $this->qty = $this->qty - 1;
    }

    public function increment(){
        $this->qty = $this->qty + 1;
    }

    public function addItem(){
        Cart::add([ 
          			
          			'id' => $this->product->id,
                    'name' => $this->product->name,
                    'qty' => $this->qty,
                    'price' => $this->product->price_partner,
                    'weight' => 550,
                    'options' => $this->options,
                   
                ]);

        $this->quantity_partner = qty_available($this->product->id);


        $this->reset('qty');

        $this->emitTo('dropdown-cart-partner', 'render');
    }
  
    public function render()
    {
        return view('livewire.add-cart-item-partner');
    }
}
