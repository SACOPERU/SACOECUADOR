<?php

namespace Database\Seeders;

use App\Models\LogoTienda;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LogoTiendaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $logo_tiendas = [
            [
                'name' => 'Logo Tienda',
                'slug' => Str::slug('Logo_tienda'),

            ]
        ];

        foreach ($logo_tiendas as $logo_tienda) {

            $logo_tienda = LogoTienda::factory(1)->create($logo_tienda)->first();

        }
    }
}
