<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderPartner;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderPartnerController extends Controller
{

    public function index()
    {
        $userId = auth()->user()->id;

        $orders = OrderPartner::where('user_id', $userId);

        if (request()->has('status')) {
            $orders->where('status', request('status'));
        }

        $orders = $orders->get();

        $reservado = OrderPartner::where('status', 1)->where('user_id', $userId)->count();
        $pagado = OrderPartner::where('status', 2)->where('user_id', $userId)->count();
        $aprobado = OrderPartner::where('status', 3)->where('user_id', $userId)->count();
        $despachado = OrderPartner::where('status', 4)->where('user_id', $userId)->count();
        $entregado = OrderPartner::where('status', 5)->where('user_id', $userId)->count();
        $anulado = OrderPartner::where('status', 6)->where('user_id', $userId)->count();

        //return view('orders.index', compact('orders', 'reservado', 'pagado', 'aprobado', 'despachado', 'entregado', 'anulado'));
    }

    public function payment(OrderPartner $order)
    {

        $items = json_decode($order->content);

        return view('orderpartners.payment', compact('order', 'items'));
    }

    public function files(OrderPartner $order, Request $request)
    {

        $request->validate([
            'file' => 'required|image|max:2048'
        ]);

        $url = Storage::put('orderspartner', $request->file('file'));

        $order->images()->create([

            'url' => $url
        ]);
    }

    public function show(OrderPartner $order)
    {

        $order->update(['status' => 2]);

        $items = json_decode($order->content);

        return view('orderpartners.show', compact('order', 'items'));
    }

    public function pdf_cotizacion(OrderPartner $order)
    {

        $items = json_decode($order->content);

        $pdf = Pdf::loadView('orderpartners.pdf_cotizacion', compact('order', 'items'));
        return $pdf->download('COTIZACION_' . $order->id . '.pdf');
    }

    public function pdf_order(OrderPartner $order)
    {

        $items = json_decode($order->content);

        $pdf = Pdf::loadView('orderpartners.pdf_order', compact('order', 'items'));
        return $pdf->download('PEDIDO_' . $order->id . '.pdf');
    }

    public function index_partner()
    {
        // Obtiene el ID del usuario autenticado
        $userId = Auth::id();

        // Filtra las órdenes del usuario logueado y que no tengan status igual a 1
        $orders = OrderPartner::where('user_id', $userId)->where('status', '<>', 0);

        // Filtra las órdenes por estado si se proporciona un estado en la solicitud
        if (request('status')) {
            $orders->where('status', request('status'));
        }

        // Obtiene todas las órdenes que cumplen con los criterios de filtrado
        $orders = $orders->get();

        // Cuenta el número de órdenes en cada estado
        $reservado = OrderPartner::where('status', 1)->where('user_id', $userId)->count();
        $pagado = OrderPartner::where('status', 2)->where('user_id', $userId)->count();
        $aprobado = OrderPartner::where('status', 3)->where('user_id', $userId)->count();
        $despachado = OrderPartner::where('status', 4)->where('user_id', $userId)->count();
        $entregado = OrderPartner::where('status', 5)->where('user_id', $userId)->count();
        $anulado = OrderPartner::where('status', 6)->where('user_id', $userId)->count();

        // Retorna la vista con las órdenes y los contadores
        return view('orderpartners.index', compact('orders', 'reservado', 'pagado', 'aprobado', 'despachado', 'entregado', 'anulado'));
    }


}
