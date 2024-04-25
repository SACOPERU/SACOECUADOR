<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Icover;
use Illuminate\Http\Request;

class IcoverController extends Controller
{
   public function index(){
        return view('admin.icovers.index');
    }
}
