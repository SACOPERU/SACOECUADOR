<?php

namespace App\Http\Livewire\Admin;

use App\Models\Promocion;

use Livewire\Component;

use Illuminate\Support\Str;

class ShowPromocion extends Component
{
    public $promocion;

    protected $listeners = ['delete'];

    protected $rules = [
        'createForm.name' => 'required',
        'createForm.slug' => 'required|unique:promocion,slug',

    ];

    protected $validationAttributes = [
        'createForm.name' => 'nombre',
        'createForm.slug' => 'slug',
    ];

    public $createForm = [
        'name' => null,
        'slug' => null,
    ];

    public $editForm = [
        'open' => false,
        'name' => null,
        'slug' => null,
    ];

    public function mount(Promocion $promocion){
        $this->promocion = $promocion;
        $this->getpromocion();
    }

    public function updatedCreateFormName($value){
        $this->createForm['slug'] = Str::slug($value);
    }

    public function updatedEditFormName($value){
        $this->editForm['slug'] = Str::slug($value);
    }

    public function save(){
        $this->validate();

        $this->promocion->promocion()->create($this->createForm);
        $this->reset('createForm');
        $this->getPromocions();
    }

    public function edit(Promocion $promocion){

        $this->resetValidation();

        $this->promocion = $promocion;

        $this->editForm['open'] = true;
        $this->editForm['name'] = $promocion->name;
        $this->editForm['slug'] = $promocion->slug;

    }

    public function update(){
        $this->validate([
            'editForm.name' => 'required',
            'editForm.slug' => 'required|unique:promocions,slug,' . $this->promocion->id,
        ]);

        $this->subcategory->update($this->editForm);
        $this->getPromocions();
        $this->reset('editForm');

    }

    public function delete(Promocion $promocion){
        $promocion->delete();
        $this->getPromocions();
    }

    public function render()
    {
        return view('livewire.admin.show-promocion')->layout('layouts.admin');
    }
}
