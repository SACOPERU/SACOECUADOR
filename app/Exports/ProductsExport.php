<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $search;

    public function __construct($search)
    {
        $this->search = $search;
    }

    public function collection()
    {
        return Product::where('name', 'like', '%' . $this->search . '%')->get();
    }

    public function headings(): array
    {
        return [

            'Nombre',
            'SKU',
            'Marca',
            'BODEGA ATOCONGO',
            'BODEGA JOCKEY PLAZA',
            'BODEGA MEGA PLAZA',
            'BODEGA HUAYLAS',
            'BODEGA PURUCHUCO',
            'BODEGA PRINCIPAL',
            'Precio',
            'Categoria',
            'SubCategoria',
            'SubFamilia',

        ];
    }

    public function map($product): array
    {

        return [
            $product->name,
            $product->sku,
            $product->brand->name,
            $product->atocong,
            $product->jockeypz,
            $product->megaplz,
            $product->huaylas,
            $product->puruchu,
            $product->principal,
            $product->price,
            $product->subcategory->category->name,
            $product->subcategory->name,
            $product->subfamilia,
        ];
    }
}
