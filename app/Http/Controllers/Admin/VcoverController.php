<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vcover;
use Illuminate\Http\Request;

class VcoverController extends Controller
{
    public function index(){
        return view('admin.vcovers.index');
    }
}
