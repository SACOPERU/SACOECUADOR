<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function show(Banner $banner){
        return view('banners.show', compact('banner'));

    }
}
