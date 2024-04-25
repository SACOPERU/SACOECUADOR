<?php

namespace App\Policies;

use App\Models\OrderPartner;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPartnerPolicy
{
    use HandlesAuthorization;

    public function authorpartner(User $user, OrderPartner $order_partner){
        if($order_partner->user_id == $user->id){
            return true;
        }else{
            return false;
        }

    }

    public function paymentpartner(User $user, OrderPartner $order_partner){
        if($order_partner->status == 2){
            return true;
        }else{
            return false;
        }
    }
}
