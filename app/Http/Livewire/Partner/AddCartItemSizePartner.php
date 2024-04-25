<?php

namespace App\Http\Livewire\Partner;

use App\Models\Size;
use Livewire\Component;

use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Storage;

class AddCartItemSizePartner extends Component
{


    public $product, $sizes;
    public $color_id = "";
    public $qty_partner = 1;
    public $quantity_partner = 0;
    public $size_id = "";

    public $colors = [];

    public $options = [];

    public function mount()
    {
        $this->sizes = $this->product->sizes;
        $this->options['image'] = Storage::url($this->product->images->first()->url);
    }

    public function updatedSizeId($value)
    {
        $size = Size::find($value);
        $this->colors = $size->colors;
        $this->options['size'] = $size->name;
        $this->options['size_id'] = $size->id;
    }

    public function updatedColorId($value)
    {
        $size = Size::find($this->size_id);
        $color = $size->colors->find($value);
        $this->quantity_partner = qty_available($this->product->id, $color->id, $size->id);
        $this->options['color'] = $color->name;
        $this->options['color_id'] = $color->id;
    }


    public function decrement()
    {
        $this->qty_partner = $this->qty_partner - 1;
    }

    public function increment()
    {
        $this->qty_partner = $this->qty_partner + 1;
    }

    public function addItempartner()
    {
        Cart::add([
            'id' => $this->product->id,
            'name' => $this->product->name,
            'qty' => $this->qty_partner,
            'price' => $this->product->price,
            'weight' => 550,
            'options' => $this->options
        ]);

        $this->quantity_partner = qty_available($this->product->id, $this->color_id, $this->size_id);

        $this->reset('qty_partner');

        $this->emitTo('partner.dropdown-cart-partner', 'render');
    }



    public function render()
    {
        return view('livewire.partner.add-cart-item-size-partner');
    }
}
