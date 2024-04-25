<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresaCanal extends Model
{
    use HasFactory;

    protected $table = 'empresa_canal';

    protected $fillable = ['empresa', 'desc_empresa', 'ruc', 'nombre_comercial', 'direccion', 'telefono01', 'telefono02', 'correo', 'logo_path', 'pais_id', 'moneda_id'];


    public function pais()
    {
        return $this->belongsTo(PaisMoneda::class, 'pais_id');
    }

    public function moneda()
    {
        return $this->belongsTo(PaisMoneda::class, 'moneda_id');
    }
}
