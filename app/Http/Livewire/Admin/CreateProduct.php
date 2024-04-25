<?php

namespace App\Http\Livewire\Admin;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Illuminate\Support\Str;

class CreateProduct extends Component
{
    public $categories, $subcategories = [],$brands =[];

    public $category_id = "", $subcategory_id = "", $brand_id = "";

    public $name, $slug, $sku, $description, $price, $quantity, $price_tachado, $price_partner, $quantity_partner;

    protected $rules=[
        'category_id' => 'required',
        'subcategory_id' => 'required',
        'name' => 'required',
        'sku'  => 'required',
        'slug' => 'required|unique:products',
        'description' => 'required',
        'brand_id' => 'required',
        'price' => 'required',
        'price_tachado' => 'required',
        'price_partner' => 'required'

    ];

    public function updatedCategoryId($value){
        $this->subcategories = Subcategory::where('category_id', $value)->get();

        $this->brands = Brand::whereHas('categories', function(Builder $query)use ($value){
            $query->where('category_id', $value);

        })->get();

        $this->reset('subcategory_id', 'brand_id');

    }

    public function updatedName($value){
        $this->slug = Str::slug($value);
    }

    public function getSubcategoryProperty(){
        return Subcategory::find($this->subcategory_id);

    }

    public function mount(){
            $this->categories = Category::all();

    }

    public function save(){

        $rules = $this->rules;


        if($this->subcategory_id){
            if(!$this->subcategory->color && !$this->subcategory->size){
                $rules['quantity'] = 'required';
                $rules['quantity_partner'] = 'required';
            }
        }
        $this->validate($rules);

        $product = new Product();
        $product->name = $this->name;
        $product->sku = $this->sku;
        $product->slug = $this->slug;
        $product->description = $this->description;
        $product->subcategory_id = $this->subcategory_id;
        $product->brand_id = $this->brand_id;
        $product->price = $this->price;
        $product->price_tachado = $this->price_tachado;
        $product->price_partner = $this->price_partner;

        if($this->subcategory_id){
            if(!$this->subcategory->color && !$this->subcategory->size){
                $product->quantity = $this->quantity;
                $product->quantity_partner = $this->quantity_partner;
            }
        }
        $product->save();

        return redirect()->route('admin.products.edit', $product);
    }

    public function render()
    {
        return view('livewire.admin.create-product')->layout('layouts.admin');
    }
}
