<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\EmpresaCanal as ModelsEmpresaCanal;
use App\Models\PaisMoneda;

class EmpresaCanal extends Component
{
    public $empresa;
    public $desc_empresa;
    public $ruc;
    public $nombre_comercial;
    public $direccion;
    public $telefono01;
    public $telefono02;
    public $correo;
    public $logo_path;
    public $pais_id;
    public $moneda_id;

    public function guardar()
    {
        $this->validate([
            'empresa' => 'required',
            'desc_empresa' => 'required',
            'ruc' => 'required',
            'nombre_comercial' =>'required',
            'direccion' => 'required',
            'telefono01' => 'required',
            'telefono02' => 'required',
            'correo' => 'required',
            'logo_path' => 'required',
            'pais_id' => 'required',
            'moneda_id' => 'required',

        ]);

        ModelsEmpresaCanal::create([
            'empresa' => $this->empresa,
            'desc_empresa' => $this->desc_empresa,
            'ruc' => $this->ruc,
            'nombre_comercial' => $this->nombre_comercial,
            'direccion' => $this->direccion,
            'telefono01' => $this->telefono01,
            'telefono02' => $this->telefono02,
            'correo' => $this->correo,
            'logo_path' => $this->logo_path,
            'pais_id' => $this->pais_id,
            'moneda_id' => $this->moneda_id,
        ]);

        $this->resetInputFields();
    }

    public function submit()
    {
        $this->guardar();

    }

    private function resetInputFields()
    {
        $this->empresa = '';
        $this->desc_empresa = '';
        $this->ruc = '';
        $this->nombre_comercial = '';
        $this->direccion = '';
        $this->telefono01 = '';
        $this->telefono02 = '';
        $this->correo = '';
        $this->logo_path = '';
        $this->pais_id = null;
        $this->moneda_id = null;
    }

    public function render()
    {
        $empresasCanales = ModelsEmpresaCanal::with(['pais', 'moneda'])->get();
        $paises = PaisMoneda::all();

        return view('livewire.admin.empresa-canal', compact('empresasCanales', 'paises'))->layout('layouts.admin');
    }

}
