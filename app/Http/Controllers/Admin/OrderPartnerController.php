<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderPartner;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;


class OrderPartnerController extends Controller
{
    public function index()
    {
        $orders = OrderPartner::query();

        if (request()->has('status')) {
            $orders->where('status', request('status'));
        }

        $orders = $orders->get();

        $reservado = OrderPartner::where('status', 1)->count();
        $pagado = OrderPartner::where('status', 2)->count();
        $aprobado = OrderPartner::where('status', 3)->count();
        $despachado = OrderPartner::where('status', 4)->count();
        $entregado = OrderPartner::where('status', 5)->count();
        $anulado = OrderPartner::where('status', 6)->count();

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

        return view('admin.orderpartners.show', compact('order', 'items'));
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
        // Filtra todas las órdenes
        $orders = OrderPartner::query();

        // Filtra las órdenes por estado si se proporciona un estado en la solicitud
        if (request('status')) {
            $orders->where('status', request('status'));
        }

        // Obtiene todas las órdenes que cumplen con los criterios de filtrado
        $orders = $orders->get();

        // Cuenta el número de órdenes en cada estado
        $reservado = OrderPartner::where('status', 1)->count();
        $pagado = OrderPartner::where('status', 2)->count();
        $aprobado = OrderPartner::where('status', 3)->count();
        $despachado = OrderPartner::where('status', 4)->count();
        $entregado = OrderPartner::where('status', 5)->count();
        $anulado = OrderPartner::where('status', 6)->count();

        // Retorna la vista con las órdenes y los contadores
        return view('admin.orderpartners.index', compact('orders', 'reservado', 'pagado', 'aprobado', 'despachado', 'entregado', 'anulado'));
    }
}
