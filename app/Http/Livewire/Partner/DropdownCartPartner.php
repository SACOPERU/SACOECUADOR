<?php

namespace App\Http\Livewire\Partner;

use Livewire\Component;

class DropdownCartPartner extends Component
{
    protected  $listeners = ['render'];

    public function render()
    {
        return view('livewire.partner.dropdown-cart-partner');
    }
}
