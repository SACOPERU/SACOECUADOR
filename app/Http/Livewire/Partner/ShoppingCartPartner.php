<?php

namespace App\Http\Livewire\Partner;

use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Livewire\Component;

class ShoppingCartPartner extends Component
{
    protected $listeners = ['render'];


    public function destroy(){
        Cart::destroy();
        $this->emitTo('partner.DropdownCartPartner', 'render');

    }

    public function delete($rowId){
        Cart::remove($rowId);

        $this->emitTo('partner.DropdownCartPartner', 'render');
    }

    public function render()
    {
        return view('livewire.partner.shopping-cart-partner')->layout('layouts.partner');
    }


}

