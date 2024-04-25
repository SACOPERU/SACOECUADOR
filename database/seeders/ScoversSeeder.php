<?php

namespace Database\Seeders;

use App\Models\Scover;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ScoversSeeder extends Seeder
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
                'name' => 'Scovers',
                'slug' => Str::slug('Scovers'),

            ]
        ];

        foreach ($scovers as $scover) {

            $scover = Scover::factory(3)->create($scover)->first();

        }
    }
}
