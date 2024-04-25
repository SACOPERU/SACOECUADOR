<?php

namespace App\Http\Livewire;

use App\Models\City;
use Livewire\Component;
use App\Models\Department;
use App\Models\District;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Models\Order;
use Illuminate\Support\Facades\Redirect;
use App\Models\Product;
use Illuminate\Http\Request;

class FormTienda extends Component
{
    public function mostrarFormulario()
    {
        // Obtener todos los productos desde la base de datos
        $products = Product::all();

        // Obtener la cantidad del carrito para el producto actual (puedes ajustar esta lógica)
        $carritoCantidad = 10; // Por ejemplo, asumimos que la cantidad del carrito es 10

        // Filtrar los productos para mostrar solo aquellos que cumplen con la condición
        $filteredProducts = $products->filter(function ($product) use ($carritoCantidad) {
            return $product->atocong >= $carritoCantidad &&
                   $product->jockeypz >= $carritoCantidad &&
                   $product->megaplz >= $carritoCantidad &&
                   $product->huaylas >= $carritoCantidad &&
                   $product->puruchu >= $carritoCantidad;
        });

        return view('livewire.create-order', compact('filteredProducts'));
    }
}
