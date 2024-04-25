<?php

namespace Database\Seeders;

use App\Models\Icover;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class IcoversSeeder extends Seeder
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
                'name' => 'Icovers',
                'slug' => Str::slug('Icovers'),

            ]
        ];

        foreach ($icovers as $icover) {

            $icover = Icover::factory(3)->create($icover)->first();

        }
    }
}
