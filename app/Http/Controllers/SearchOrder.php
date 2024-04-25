<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class SearchOrder extends Controller
{
    public function __invoke(Request $request)
    {

       $id = $request -> id;

       $orders = Order::where('id', 'LIKE' ,'%' . $id . '%')
                                ->paginate(8);

        return view('search', compact('orders'));
    }
}
