<?php

namespace App\Http\Controllers\Xiaomi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Order;
use App\Models\Xcover;
use App\Models\Promocion;
use Illuminate\Support\Facades\Session;

class WelcomeController extends Controller
{
    public function __invoke()
    {
        if (auth()->user()) {
            $reservedOrdersCount = Order::where('status', 1)
                ->where('user_id', auth()->user()->id)
                ->count();

            if ($reservedOrdersCount) {
                $message = "Usted tiene $reservedOrdersCount órdenes reservadas. <a class='font-bold' href='" . route('orders.index') . "?status=1'>Ir a pagar</a>";

                // Guardar la fecha y hora actual en la sesión
                Session::put('flash.banner_time', now());
                session()->flash('flash.banner', $message);
            }
        }

        // Verificar si ha pasado el tiempo especificado (3 minutos) y eliminar el mensaje
        $bannerTime = Session::get('flash.banner_time');
        if ($bannerTime && $bannerTime->diffInMinutes(now()) >= 3) {
            Session::forget(['flash.banner_time', 'flash.banner']);
        }


        // Resto del código
        $categories = Category::whereIn('slug', ['smartphones', 'smart-home', 'life-style'])->get();
        $xcovers = Xcover::orderBy('id', 'asc')->get();
        $promocions = Promocion::orderBy('id', 'asc')->get();

        return view('xiaomi.dashboard', compact('categories', 'xcovers', 'promocions'));
    }
}
