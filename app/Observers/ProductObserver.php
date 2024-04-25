<?php

namespace App\Observers;
use App\Models\Product;
use App\Models\Subcategory;

class ProductObserver
{
    public function update(Product $product){
        $subcategory_id = $product->subcategory_id;
        $subcategory = Subcategory::find($subcategory_id);

        if ($subcategory->size) {
            if ($product->colors->count()) {
                $product->colors()->detach();
            }
        }elseif ($subcategory->color) {
            if ($product->size->count()) {
                foreach($product->size as $size){
                    $size->delete();
                }
            }
        }else{
            if ($product->colors->count()) {
                $product->colors()->detach();
            }

            if ($product->size->count()) {
                foreach($product->size as $size){
                    $size->delete();
                }
            }
        }
    }

}

