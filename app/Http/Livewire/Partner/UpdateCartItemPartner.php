<?php

namespace App\Http\Livewire\Partner;

use Livewire\Component;
use Gloudemans\Shoppingcart\Facades\Cart;

class UpdateCartItemPartner extends Component
{

    public $rowId, $qty_partner, $quantity_partner;

    public function mount(){
        $item = Cart::get($this->rowId);
        $this->qty_partner = $item->qty_partner;

        $this->quantity_partner = qty_available($item->id);
    }

    public function decrement(){
        $this->qty_partner = $this->qty_partner - 1;

        Cart::update($this->rowId, $this->qty_partner);

        $this->emit('render');
    }

    public function increment(){
        $this->qty_partner = $this->qty_partner + 1;

        Cart::update($this->rowId, $this->qty_partner);

        $this->emit('render');
    }


    public function render()
    {
        return view('livewire.partner.update-cart-item-partner');
    }
}
