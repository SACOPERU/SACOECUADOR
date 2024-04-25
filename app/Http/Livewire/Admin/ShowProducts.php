<?php
// app/Http/Livewire/Admin/ShowProducts.php

namespace App\Http\Livewire\Admin;

use App\Exports\ProductsExport; // Ajusta el namespace según tu estructura de carpetas
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class ShowProducts extends Component
{
    use WithPagination;

    public $search;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function exportToExcel()
    {
        return Excel::download(new ProductsExport($this->search), 'products.xlsx');
    }

    public function render()
    {
        $products = Product::where(function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('sku', 'like', '%' . $this->search . '%');
        })->paginate(10);

        return view('livewire.admin.show-products', compact('products'))->layout('layouts.admin');
    }
}