<?php

namespace Database\Seeders;

use App\Models\Vcover;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VcoversSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
     public function run()
    {
        $scovers = [
            [
                'name' => 'Vcovers',
                'slug' => Str::slug('Vcovers'),

            ]
        ];

        foreach ($vcovers as $vcover) {

            $vcover = Vcover::factory(3)->create($vcover)->first();

        }
    }
}
