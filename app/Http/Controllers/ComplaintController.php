<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;


class ComplaintController extends Controller
{
    public function render(Request $request)
    {
         return view('complaint');
    }
}


