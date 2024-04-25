<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CanalSubcanal extends Model
{
    use HasFactory;

    protected $table = 'canal_subcanal';

    protected $fillable = [
        'canal',
        'desc_canal',
        'subcanal',
        'desc_subcanal',
        'modelo_negocio',
        'bodega',
        'tipo_distribucion',
        'lp_visual',
        'desc_lp_visual',
        'lp_neto',
        'desc_lp_neto',
        'idflexline_visual',
        'idflexline_neto'

    ];
}
