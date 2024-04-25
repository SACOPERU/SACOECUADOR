<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Models\Category;
use App\Models\LogoTienda;

class Infinix extends Component
{
    public function render()
    {
         $categories = Category::where('slug', 'smartphones-i')->get();
        $logo_tiendas = LogoTienda::orderBy('id', 'asc')->get();
    
        return view('livewire.marcas.infinix', compact('categories', 'logo_tiendas'));
    }
    
}
