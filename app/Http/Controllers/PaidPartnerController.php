<?php

namespace App\Http\Controllers;


use App\Models\OrderPartner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Queue\Listener;

class PaidPartnerController extends Controller
{
    public function izipay(OrderPartner $order_partner,Request $request){


        $answer = json_decode($request->get("kr-answer"));
        $orderDetails = $answer->orderDetails;

        $orderId = $orderDetails->orderId;
        $order_partner= OrderPartner::findOrFail($orderId);
            $uptadestatus = $answer->orderStatus;

        if($uptadestatus == 'PAID'){

        $order_partner->status = 2;

            $order_partner->save();
        }
        return redirect()->route('orderpartners.show', $order_partner);
        }
}
