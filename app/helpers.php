<?php

use App\Models\Product;
use App\Models\Color;
use App\Models\Size;
use Gloudemans\Shoppingcart\Facades\Cart;

function quantity($product_id, $color_id = null, $size_id = null){
    $product = Product::find($product_id);

    if($size_id){
        $size = Size::find($size_id);
        $quantity = optional($size->colors->find($color_id))->pivot->quantity ?? 0;
    } elseif($color_id){
        $quantity = optional($product->colors->find($color_id))->pivot->quantity ?? 0;
    } else {
        $quantity = $product->quantity ?? 0;
    }

    return $quantity;
}

function quantity_partner($product_id, $color_id = null, $size_id = null){
    $product = Product::find($product_id);

    if($size_id){
        $size = Size::find($size_id);
        $quantity_partner = optional($size->colors->find($color_id))->pivot->quantity_partner ?? 0;
    } elseif($color_id){
        $quantity_partner = optional($product->colors->find($color_id))->pivot->quantity_partner ?? 0;
    } else {
        $quantity_partner = $product->quantity_partner ?? 0;
    }

    return $quantity_partner;
}

function qty_added($product_id, $color_id = null, $size_id = null){
    $item = Cart::content()
        ->where('id', $product_id)
        ->where('options.color_id', $color_id)
        ->where('options.size_id', $size_id)
        ->first();

    return $item ? $item->qty : 0;
}

function qty_available($product_id, $color_id = null, $size_id = null){
    $quantity = quantity($product_id, $color_id, $size_id);
    $qty_added = qty_added($product_id, $color_id, $size_id);

    return $quantity - $qty_added;
}

function discount($item){
    if ($item && $item->options && $item->options->color_id) {
        $product = Product::find($item->id);
        $qty_available = qty_available($item->id, $item->options->color_id, $item->options->size_id);

        if ($item->options->size_id) {
            $size = Size::find($item->options->size_id);
            $size->colors()->detach($item->options->color_id);
            $size->colors()->attach([
                $item->options->color_id => ['quantity' => $qty_available]
            ]);
        } elseif ($item->options->color_id) {
            $product->colors()->detach($item->options->color_id);
            $product->colors()->attach([
                $item->options->color_id => ['quantity' => $qty_available]
            ]);
        } else {
            $product->quantity = $qty_available;
            $product->save();
        }
    }
}

function increase($item){
    if ($item && $item->options && $item->options->color_id) {
        $product = Product::find($item->id);
        $quantity = quantity($item->id, $item->options->color_id, $item->options->size_id) + $item->qty;

        if ($item->options->size_id) {
            $size = Size::find($item->options->size_id);
            $size->colors()->detach($item->options->color_id);
            $size->colors()->attach([
                $item->options->color_id => ['quantity' => $quantity]
            ]);
        } elseif ($item->options->color_id) {
            $product->colors()->detach($item->options->color_id);
            $product->colors()->attach([
                $item->options->color_id => ['quantity' => $quantity]
            ]);
        } else {
            $product->quantity = $quantity;
            $product->save();
        }
    }
}
