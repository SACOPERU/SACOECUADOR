<?php

namespace Database\Seeders;

use App\Models\Xcover;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class XcoversSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $xcovers = [
            [
                'name' => 'Xcovers',
                'slug' => Str::slug('Xcovers'),

            ]
        ];

        foreach ($xcovers as $xcover) {

            $xcover = Xcover::factory(3)->create($xcover)->first();

        }
    }
}
