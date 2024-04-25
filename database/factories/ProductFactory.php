<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Factories\Factory;

use Illuminate\Support\Str;

class ProductFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @var string
     */

     protected $model = Product::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $name = $this->faker->sentence(2);
        $sku  = $this->faker->sentence(1);
        $bodega  = $this->faker->sentence(1);

         $atocong = $this->faker->sentence(2);
        $jockeypz = $this->faker->sentence(2);
        $megaplz = $this->faker->sentence(2);
        $huaylas = $this->faker->sentence(2);
        $puruchu = $this->faker->sentence(2);

        $subcategory = Subcategory::all()->random();
        $category = $subcategory->category;

        $brand = $category->brands->random();

        if($subcategory->color){
            $quantity = null;
            $quantity_partner = null;
            $stock_flex = null;



        }else{
            $quantity = 2;
            $quantity_partner = 2;
            $stock_flex = 2;

        }

        return [

            'name' => $name,
            'sku'  => $sku,
            'slug'=> Str::slug($name),
            'description'=> $this->faker->text(),
            'atocong' => $atocong,
            'jockeypz' => $jockeypz,
            'megaplz' => $megaplz,
            'huaylas' => $huaylas,
            'puruchu' => $puruchu,
            'bodega' => $bodega,
            'price'=> $this->faker->randomElement([19.99, 49.99, 99.99]),
            'price_tachado'=> $this->faker->randomElement([100.99, 149.99, 199.99]),
            'price_partner'=> $this->faker->randomElement([10.99, 9.99, 15.99]),
            'subcategory_id'=> $subcategory->id,
            'brand_id' => $brand->id,
            'quantity'=> $quantity,
            'quantity_partner'=> $quantity_partner,
            'stock_flex' => $stock_flex,
            'status' =>2,
            'destacado' =>1
        ];

    }
}








