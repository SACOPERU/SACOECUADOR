<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Models\Category;
use App\Models\LogoTienda;

class Vivo extends Component
{
       public function render()
    {

        $categories = Category::where('slug', 'smartphones-v')->get();
        $logo_tiendas = LogoTienda::orderBy('id', 'asc')->get();


        return view('livewire.marcas.vivo', compact('categories', 'logo_tiendas'));
    }
}
