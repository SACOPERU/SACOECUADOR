<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parametrizado extends Model
{
    use HasFactory;

    protected $table = 'parametrizado';

    protected $fillable = [
        'empresa_id',
        'desc_empresa_id',
        'canal_id',
        'desc_canal_id',
        'subcanal_id',
        'desc_subcanal_id',
        'modelo_negocio_id',
        'bodega_id',
        'tipo_distribucion_id',

        'idflexline_visual_id',
        'lp_visual_id',
        'desc_lp_visual_id',

        'idflexline_neto_id',
        'lp_neto_id',
        'desc_lp_neto_id',
    ];

    public function empresa()
    {
        return $this->belongsTo(EmpresaCanal::class, 'empresa_id');
    }

    public function desc_empresa()
    {
        return $this->belongsTo(EmpresaCanal::class, 'desc_empresa_id');
    }

    public function canal()
    {
        return $this->belongsTo(CanalSubcanal::class, 'canal_id');
    }

    public function desc_canal()
    {
        return $this->belongsTo(CanalSubcanal::class, 'desc_canal_id');
    }

    public function subcanal()
    {
        return $this->belongsTo(CanalSubcanal::class, 'subcanal_id');
    }

    public function desc_subcanal()
    {
        return $this->belongsTo(CanalSubcanal::class, 'desc_subcanal_id');
    }

    public function modelo_negocio()
    {
        return $this->belongsTo(CanalSubcanal::class, 'modelo_negocio_id');
    }

    public function bodega()
    {
        return $this->belongsTo(CanalSubcanal::class, 'bodega_id');
    }

    public function tipo_distribucion()
    {
        return $this->belongsTo(CanalSubcanal::class, 'tipo_distribucion_id');
    }



    public function idflexline_visual()
    {
        return $this->belongsTo(CanalSubcanal::class, 'idflexline_visual_id');
    }

    public function lp_visual()
    {
        return $this->belongsTo(CanalSubcanal::class, 'lp_visual_id');
    }

    public function desc_lp_visual()
    {
        return $this->belongsTo(CanalSubcanal::class, 'desc_lp_visual_id');
    }



    public function idflexline_neto()
    {
        return $this->belongsTo(CanalSubcanal::class, 'idflexline_neto_id');
    }

    public function lp_neto()
    {
        return $this->belongsTo(CanalSubcanal::class, 'lp_neto_id');
    }

    public function desc_lp_neto()
    {
        return $this->belongsTo(CanalSubcanal::class, 'desc_lp_neto_id');
    }

    // Agrega más relaciones según sea necesario
}
