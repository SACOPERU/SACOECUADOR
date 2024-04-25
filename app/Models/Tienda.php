<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tienda extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $fillable = [

        'atocong',
        'jockeypz',
        'megaplz',
        'huaylas',
        'puruchu',
    ];
}
