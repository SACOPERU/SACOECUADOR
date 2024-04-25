<?php

namespace Database\Seeders;

use App\Models\Promocion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PromocionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $promocions = [
            [
                'name' => 'Promocion Imagen',
                'slug' => Str::slug('Promocion Imagen'),
                 'ruta' => '/'
            ]
        ];

        foreach ($promocions as $promocion) {

            $promocion = Promocion::factory(3)->create($promocion)->first();

        }

    }
}
