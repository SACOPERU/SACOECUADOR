<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class CategoryFilterPartner extends Component
{
    use WithPagination;

    public $marca;

    public $view = "grid";

    protected $queryString = ['marca'];


    public function limpiar()
    {
        $this->reset(['marca', 'page']);
    }

    public function updatedSubcategoria()
    {
        $this->resetPage();
    }

    public function updatedMarca()
    {
        $this->resetPage();
    }

    // app/Http/Livewire/CategoryFilterPartner.php

public function render()
{
    $productsQuery = Product::where('status', 2)
        ->where('price_partner', '>', 0)
        ->where('quantity_partner', '>', 0);

    // Aplicar el filtro por marca si se selecciona una marca específica
    if ($this->marca) {
        $productsQuery->where('marca', $this->marca);
    }

    $products = $productsQuery->paginate(20);

    return view('livewire.category-filter-partner', compact('products'));
}

}
