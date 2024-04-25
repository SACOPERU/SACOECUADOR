<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;

class ConsultaProductos extends Component
{
    public function render()
    {
        return view('livewire.admin.consulta-productos')->layout('layouts.admin');
    }
}
