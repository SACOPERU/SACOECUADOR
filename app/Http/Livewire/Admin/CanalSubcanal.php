<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\CanalSubcanal as ModelsCanalSubcanal;

class CanalSubcanal extends Component
{
    public $canal;
    public $desc_canal;
    public $subcanal;
    public $desc_subcanal;
    public $modelo_negocio;
    public $bodega;
    public $tipo_distribucion;
    public $lp_visual;
    public $desc_lp_visual;
    public $lp_neto;
    public $desc_lp_neto;
    public $idflexline_visual;
    public $idflexline_neto;

    public function guardar()
    {
        $this->validate([
            'canal' => 'required',
            'desc_canal' => 'required',
            'subcanal' => 'required',
            'desc_subcanal' => 'required',
            'modelo_negocio' => 'required',
            'bodega' => 'required',
            'tipo_distribucion' => 'required',
            'lp_visual' => 'required',
            'desc_lp_visual' => 'required',
            'lp_neto' => 'required',
            'desc_lp_neto' => 'required',
            'idflexline_visual' => 'required',
            'idflexline_neto' => 'required',

        ]);

        ModelsCanalSubcanal::create([
            'canal' => $this->canal,
            'desc_canal' => $this->desc_canal,
            'subcanal' => $this->subcanal,
            'desc_subcanal' => $this->desc_subcanal,
            'modelo_negocio' => $this->modelo_negocio,
            'bodega' => $this->bodega,
            'tipo_distribucion' => $this->tipo_distribucion,
            'lp_visual' => $this->lp_visual,
            'desc_lp_visual' => $this->desc_lp_visual,
            'lp_neto' => $this->lp_neto,
            'desc_lp_neto' => $this->desc_lp_neto,

            'idflexline_visual' => $this->idflexline_visual,
            'idflexline_neto' => $this->idflexline_neto,
        ]);

        $this->resetInputFields();
        //$this->emit('saved');
    }

    private function resetInputFields()
    {
        $this->canal = '';
        $this->desc_canal = '';
        $this->subcanal = '';
        $this->desc_subcanal = '';
        $this->modelo_negocio = '';
        $this->bodega = '';
        $this->tipo_distribucion = '';
        $this->lp_visual = '';
        $this->desc_lp_visual = '';
        $this->lp_neto = '';
        $this->desc_lp_neto = '';
        $this->idflexline_visual = '';
        $this->idflexline_neto = '';
    }

    public function render()
    {
        $canalSubcanales = ModelsCanalSubcanal::all();

        return view('livewire.admin.canal-subcanal', compact('canalSubcanales'))->layout('layouts.admin');
    }
}
