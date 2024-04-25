<?php

namespace App\Http\Livewire\Admin;

use App\Http\Controllers\Admin\PromocionController;
use App\Models\Promocion;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class PromocionImagen extends Component
{

    use WithFileUploads;
    public $promocions,$promocion,$rand;

    protected $listeners = ['delete'];

    public $createForm =[

        'name' => null,
        'slug' => null,
        'image' => null,


    ];

    public $editForm =[
        'open' => false,
        'name' => null,
        'slug' => null,
        'image' => null,

    ];

    public $editImage;

    protected $rules = [
        'createForm.name' => 'required',
        'createForm.slug' => 'required|unique:promocions,slug',
        'createForm.image' => 'required|image|max:650',
    ];

    protected $validationAttributes = [
        'createForm.name' => 'nombre',
        'createForm.slug' => 'slug',
        'createForm.image' => 'imagen',


        'editForm.name' => 'nombre',
        'editForm.slug' => 'slug',
        'editImage' => 'imagen',

    ];

    public function mount(){

        $this->getPromocions();
        $this->rand = rand();
    }

    public function updatedCreateFormName($value){
        $this->createForm['slug'] = Str::slug($value);
    }

    public function updatedEditFormName($value){
        $this->editForm['slug'] = Str::slug($value);
    }


    public function getPromocions(){
        $this->promocions = Promocion::all();
    }

    public function save(){

        $this->validate();

        $image = $this->createForm['image']->store('promocions');

       $promocion = Promocion::create([
            'name' => $this->createForm['name'],
            'slug' => $this->createForm['slug'],
            'image' => $image
        ]);


        $this->rand = rand();
        $this->reset('createForm');

        $this->getPromocions();
        $this->emit('saved');
    }

    public function edit(Promocion $promocion){

        $this->reset(['editImage']);

        $this->resetValidation();

        $this->promocion = $promocion;

        $this->editForm['open'] = true;
        $this->editForm['name'] = $promocion->name;
        $this->editForm['slug'] = $promocion->slug;
        $this->editForm['image'] = $promocion->image;

    }

    public function update(){

        $rules = [
            'editForm.name' => 'required',
            'editForm.slug' => 'required|unique:promocions,slug,' . $this->promocion->id,
        ];

        if ($this->editImage) {
            $rules['editImage'] = 'image|max:1024';
        }

        $this->validate($rules);

        if ($this->editImage) {
            Storage::delete($this->editForm['image']);

            $this->editForm['image'] = $this->editImage->store('promocions');
        }

        $this->promocion->update($this->editForm);



        $this->reset(['editForm','editImage']);

        $this->getPromocions();
    }

    public function delete(Promocion $promocion){
        $promocion->delete();
        $this->getPromocions();
    }

    public function render()
    {
        return view('livewire.admin.promocion-imagen');
    }
}
