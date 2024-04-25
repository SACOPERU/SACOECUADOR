<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Province extends Model
{
    protected $fillable = ['name', 'department_id'];


    
    public function cities(){

        return $this->hasMany(City::class);
    }

    
}





