<?php

namespace App\Http\Livewire\Partner;

use Livewire\Component;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Storage;
use App\Models\Color;

class AddCartItemColorPartner extends Component
{
    public $product, $colors;
    public $color_id = "";

    public $qty_partner = 1;
    public $quantity_partner = 0;

    public $options = [
        'size_id' => null
    ];

    public function mount(){
        $this->colors = $this->product->colors;
        $this->options['image'] = Storage::url($this->product->images->first()->url);
    }

    public function updatedColorId($value){
        $color = $this->product->colors->find($value);
        $this->quantity_partner = qty_available($this->product->id, $color->id);
        $this->options['color'] = $color->name;
        $this->options['color_id'] = $color->id;
    }

    public function decrement(){
        $this->qty_partner = $this->qty_partner - 1;
    }

    public function increment(){
        $this->qty_partner = $this->qty_partnery + 1;
    }

    public function addItem(){
        Cart::add([ 'id' => $this->product->id,
                    'name' => $this->product->name,
                    'qty' => $this->qty_partner,
                    'price' => $this->product->price,
                    'weight' => 550,
                    'options' => $this->options
                ]);

        $this->quantity_partner = qty_available($this->product->id, $this->color_id);

        $this->reset('qty');

        $this->emitTo('partner.dropdown-cart-partner', 'render');
    }

    public function render()
    {
        return view('livewire.partner.add-cart-item-color-partner');
    }
}
