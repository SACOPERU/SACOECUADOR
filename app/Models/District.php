<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $fillable = ['name','cost', 'city_id','zona','lead_time'];

    public function orders(){
        return $this->hasMany(Order::class);
    }

    public function order_partners(){
        return $this->hasMany(OrderPartner::class);
    }
  
      public function city()
    {
        return $this->belongsTo(City::class);
    }
}
