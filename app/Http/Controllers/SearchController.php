<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $name = $request->input('name');
        $subfamilia = $request->input('subfamilia');

        $productsQuery = Product::where('status', 2);

        if ($name) {
            $productsQuery->where('name', 'LIKE', '%' . $name . '%');
        }

        if ($subfamilia && $productsQuery->count() === 0) {
            return redirect()->route('search', ['subfamilia' => $subfamilia]);
        }

        $products = $productsQuery->paginate(8);

        return view('search', compact('products'));
    }
}
