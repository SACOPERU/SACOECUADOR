<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Parametrizado;
use App\Models\EmpresaCanal;
use App\Models\CanalSubcanal;

class Parametrizacion extends Component
{
    public $empresa_id = '';
    public $desc_empresa_id = '';
    public $canal_id = '';
    public $desc_canal_id = '';
    public $subcanal_id = '';
    public $desc_subcanal_id = '';
    public $modelo_negocio_id = '';
    public $bodega_id = '';
    public $tipo_distribucion_id = '';
    public $lp_visual_id = '';
    public $desc_lp_visual_id = '';
    public $lp_neto_id = '';
    public $desc_lp_neto_id = '';
    public $idflexline_visual_id = '' ;
    public $idflexline_neto_id = '' ;

    public function guardar()
    {
        $this->validate([
            'empresa_id' => 'required',
            'desc_empresa_id' => 'required',
            'canal_id' => 'required',
            'desc_canal_id' => 'required',
            'subcanal_id' => 'required',
            'desc_subcanal_id' => 'required',
            'modelo_negocio_id' => 'required',
            'bodega_id' => 'required',
            'tipo_distribucion_id' => 'required',
            'lp_visual_id' => 'required',
            'desc_lp_visual_id' => 'required',
            'lp_neto_id' => 'required',
            'desc_lp_neto_id' => 'required',

            'idflexline_visual_id' => 'required',
            'idflexline_neto_id' => 'required',
        ]);

        Parametrizado::create([
            'empresa_id' => $this->empresa_id,
            'desc_empresa_id' => $this->desc_empresa_id,
            'canal_id' => $this->canal_id,
            'desc_canal_id' => $this->desc_canal_id,
            'subcanal_id' => $this->subcanal_id,
            'desc_subcanal_id' => $this->desc_subcanal_id,
            'modelo_negocio_id' => $this->modelo_negocio_id,
            'bodega_id' => $this->bodega_id,
            'tipo_distribucion_id' => $this->tipo_distribucion_id,
            'lp_visual_id' => $this->lp_visual_id,
            'desc_lp_visual_id' => $this->desc_lp_visual_id,
            'lp_neto_id' => $this->lp_neto_id,
            'desc_lp_neto_id' => $this->desc_lp_neto_id,

            'idflexline_visual_id' => $this->idflexline_visual_id,
            'idflexline_neto_id' => $this->idflexline_neto_id,
        ]);

        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->empresa_id = null;
        $this->desc_empresa_id = null;
        $this->canal_id = null;
        $this->desc_canal_id = null;
        $this->subcanal_id = null;
        $this->desc_subcanal_id = null;
        $this->modelo_negocio_id = null;
        $this->bodega_id = null;
        $this->tipo_distribucion_id = null;
        $this->lp_visual_id = null;
        $this->desc_lp_visual_id = null;
        $this->lp_neto_id = null;
        $this->desc_lp_neto_id = null;

        $this->idflexline_visual_id = null;
        $this->idflexline_neto_id = null;
    }

    public function submit()
    {
        $this->guardar();
    }

    public function render()
    {
        // Recupera las relaciones necesarias para la vista
        $parametrizados = Parametrizado::with(['empresa', 'canal'])->get();
        $empresas = EmpresaCanal::all();
        $canales = CanalSubcanal::all();

        return view('livewire.admin.parametrizacion', compact('parametrizados', 'empresas', 'canales'))->layout('layouts.admin');
    }
}
