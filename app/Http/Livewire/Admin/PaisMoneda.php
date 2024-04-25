<?php

namespace App\Http\Livewire\Admin;

use App\Models\PaisMoneda as ModelsPaisMoneda;
use Livewire\Component;

class PaisMoneda extends Component
{
    public $pais;
    public $desc_pais;
    public $moneda;
    public $desc_moneda;

    public function guardar()
    {
        $this->validate([
            'pais' => 'required',
            'desc_pais' => 'required',
            'moneda' => 'required|in:PEN,USD',
            'desc_moneda' => 'required',
        ]);

        ModelsPaisMoneda::create([
            'pais' => $this->pais,
            'desc_pais' => $this->desc_pais,
            'moneda' => $this->moneda,
            'desc_moneda' => $this->desc_moneda,
        ]);

        $this->resetInputFields();
    }

    public function submit()
    {
        $this->guardar();

    }

    private function resetInputFields()
    {
        $this->pais = '';
        $this->desc_pais = '';
        $this->moneda = '';
        $this->desc_moneda = '';
    }

    public function render()
    {
        $paisesMonedas = ModelsPaisMoneda::all();
        return view('livewire.admin.pais-moneda', compact('paisesMonedas'))->layout('layouts.admin');
    }
}
