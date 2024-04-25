<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaisMoneda extends Model
{
    use HasFactory;

    protected $table = 'pais_moneda';

    protected $fillable = ['pais', 'desc_pais', 'moneda', 'desc_moneda'];
}
