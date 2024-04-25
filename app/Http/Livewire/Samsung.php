<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Models\Category;
use App\Models\LogoTienda;

class Samsung extends Component
{
    public function render()
    {
        $categories = Category::whereIn('slug', ['moviles'])->get();
        $logo_tiendas = LogoTienda::orderBy('id', 'asc')->get();

        return view('livewire.marcas.samsung', compact('categories', 'logo_tiendas'));
    }
}
