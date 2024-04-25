<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function show(Category $category)
  {
    // Verifica el nombre de la categoría y carga la vista correspondiente
    switch ($category->slug) {
        
        case 'smartphones-v':
            return view('categories.show_v', compact('category'));
        case 'smartphones-i':
            return view('categories.show_i', compact('category'));
        case 'moviles':
            return view('categories.show_s', compact('category'));
        default:
            return view('categories.show', compact('category'));
    }
  }

}
