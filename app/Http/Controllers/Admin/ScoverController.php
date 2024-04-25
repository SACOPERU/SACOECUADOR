<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scover;
use Illuminate\Http\Request;

class ScoverController extends Controller
{
    public function index(){
        return view('admin.scovers.index');
    }
}
