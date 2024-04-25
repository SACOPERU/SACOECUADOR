<?php

namespace Database\Factories;

use App\Models\LogoTienda;
use Illuminate\Database\Eloquent\Factories\Factory;

class LogoTiendaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = LogoTienda::class;



    public function definition()
    {

            return [
                'image' => 'logo_tiendas/' . $this->faker->image('public/storage/logo_tiendas', 1200, 1500, null, false)
             ];

    }
}
