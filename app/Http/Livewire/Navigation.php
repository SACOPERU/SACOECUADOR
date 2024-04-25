<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Models\Category;
use App\Models\LogoTienda;

class Navigation extends Component
{
    public function render()
    {

        $categories = Category::where('name', '!=', 'default')->get();
        $logo_tiendas = LogoTienda::orderBy('id', 'asc')->get();


        return view('livewire.navigation', compact('categories', 'logo_tiendas'));
    }
}

