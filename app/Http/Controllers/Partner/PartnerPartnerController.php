<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Session;

class PartnerPartnerController extends Controller
{
    public function __invoke()
    {
        if (auth()->user()) {
            $reservedOrdersCount = Order::where('status', 1)
                ->where('user_id', auth()->user()->id)
                ->count();
        }

        // Resto del código

        $products = Product::where('name', '!=', 'default')
            ->where('price_partner', '>', 0)
            ->whereNotNull('price_partner')
            ->get();
      

        return view('partner.dashboard', compact('products'));
    }
}
