<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $banners = [
            [
                'name' => 'Banners',
                'slug' => Str::slug('Banners'),

            ]
        ];

        foreach ($banners as $banner) {

            $banner = Banner::factory(3)->create($banner)->first();

        }

    }
    }


    