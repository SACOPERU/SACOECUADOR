<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;

class StatusProduct extends Component
{

    public $product , $status , $destacado ,$product_principal;

    public  function mount(){

        $this->status = $this->product->status;

        $this->destacado = $this->product->destacado;

        $this->product_principal = $this->product->product_principal;

    }

    public function save(){

        $this->product->status = $this->status;
        $this->product->destacado = $this->destacado;
        $this->product->product_principal = $this->product_principal;

        $this->product->save();

        $this->emit('saved');

    }

    public function render()
    {
        return view('livewire.admin.status-product');
    }
}
