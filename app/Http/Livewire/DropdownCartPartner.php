<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Gloudemans\Shoppingcart\Facades\Cart;

class DropdownCartPartner extends Component
{
  
  	protected $listeners = ['render'];


    public function delete($rowId){
        Cart::remove($rowId);

        $this->emitTo('DropdownCartPartner', 'render');

    }
  
    public function render()
    {
        return view('livewire.dropdown-cart-partner');
    }
}
