<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogoTienda extends Model
{
    use HasFactory;

    protected $fillable = ['name','slug', 'image'];


    public function getRouteKeyName(){
        return 'slug';
    }
}
