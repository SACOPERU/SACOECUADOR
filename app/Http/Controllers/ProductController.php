<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        if (str_starts_with($product->sku, 'SM')) {
            return view('products.show_s', compact('product'));
        } elseif (str_starts_with($product->sku, 'TMG')) {
            return view('products.show_v', compact('product'));
        } elseif (str_starts_with($product->sku, 'INF')) {
            return view('products.show_i', compact('product'));
        }
    
        return view('products.show', compact('product'));
    }
    
}
