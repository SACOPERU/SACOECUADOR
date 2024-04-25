<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Models\Category;
use App\Models\LogoTienda;

class Xiaomi extends Component
{
       public function render()
    {

       $categories = Category::whereIn('slug', ['smartphones', 'smart-home', 'life-style'])->get();
        $logo_tiendas = LogoTienda::orderBy('id', 'asc')->get();

        return view('livewire.marcas.xiaomi', compact('categories', 'logo_tiendas'));
    }
}
