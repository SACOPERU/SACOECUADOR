<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Complaint;
use App\Models\DocumentType;
use App\Models\Department;
use App\Models\Province;
use App\Models\District;
use App\Models\City;
use App\Models\Document;

class ComplaintForm extends Component
{
    public $documento_id, $numero_documento, $primer_nombre, $segundo_nombre, $apellido_paterno, $apellido_materno, $departamento_id, $provincia_id, $distrito_id, $ciudad_id, $telefono, $email;

    public $documentTypes, $departments, $provinces, $districts, $cities;

    public function mount()
    {
        $this->documentTypes = Document::all();
        $this->departments = Department::all();
        $this->provinces = Province::all();
        $this->districts = District::all();
        $this->cities = City::all();
    }

    public function submit()
    {
        $this->validate([
            'documento_id' => 'required',
            'numero_documento' => 'required|max:255',
            'primer_nombre' => 'required|max:255',
            'segundo_nombre' => 'nullable|max:255',
            'apellido_paterno' => 'required|max:255',
            'apellido_materno' => 'nullable|max:255',
            'departamento_id' => 'required',
            'provincia_id' => 'required',
            'distrito_id' => 'required',
            'ciudad_id' => 'required',
            'telefono' => 'nullable|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        Complaint::create([
            'documento_id' => $this->documento_id,
            'numero_documento' => $this->numero_documento,
            'primer_nombre' => $this->primer_nombre,
            'segundo_nombre' => $this->segundo_nombre,
            'apellido_paterno' => $this->apellido_paterno,
            'apellido_materno' => $this->apellido_materno,
            'departamento_id' => $this->departamento_id,
            'provincia_id' => $this->provincia_id,
            'distrito_id' => $this->distrito_id,
            'ciudad_id' => $this->ciudad_id,
            'telefono' => $this->telefono,
            'email' => $this->email,
        ]);

        session()->flash('message', 'Complaint successfully created.');
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->documento_id = '';
        $this->numero_documento = '';
        $this->primer_nombre = '';
        $this->segundo_nombre = '';
        $this->apellido_paterno = '';
        $this->apellido_materno = '';
        $this->departamento_id = '';
        $this->provincia_id = '';
        $this->distrito_id = '';
        $this->ciudad_id = '';
        $this->telefono = '';
        $this->email = '';
    }
	
      public function render()
    {
        return view('livewire.complaint');
    }

}


