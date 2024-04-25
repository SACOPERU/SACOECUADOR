<?php

namespace App\Http\Livewire\Admin;

use App\Http\Controllers\Admin\LogoTiendaController;
use App\Models\LogoTienda as Logotiends;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class LogoTienda extends Component
{

    use WithFileUploads;
    public $logo_tiendas, $logo_tienda, $rand;

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
        'createForm.slug' => 'required|unique:logo_tiendas,slug',
        'createForm.image' => 'required|image|max:1024',
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

        $this->getLogo_tiendas();
        $this->rand = rand();
    }

    public function updatedCreateFormName($value){
        $this->createForm['slug'] = Str::slug($value);
    }

    public function updatedEditFormName($value){
        $this->editForm['slug'] = Str::slug($value);
    }


    public function getLogo_tiendas(){
        $this->logo_tiendas = Logotiends::all();
    }

    public function save(){

        $this->validate();

        $image = $this->createForm['image']->store('banners');

       $banner = Logotiends::create([
            'name' => $this->createForm['name'],
            'slug' => $this->createForm['slug'],
            'image' => $image
        ]);


        $this->rand = rand();
        $this->reset('createForm');

        $this->getLogo_tiendas();
        $this->emit('saved');
    }
    public function edit(Logotiends $logo_tienda){

        $this->reset(['editImage']);

        $this->resetValidation();

        $this->logo_tienda = $logo_tienda;

        $this->editForm['open'] = true;
        $this->editForm['name'] = $logo_tienda->name;
        $this->editForm['slug'] = $logo_tienda->slug;
        $this->editForm['image'] = $logo_tienda->image;

    }

    public function update(){

        $rules = [
            'editForm.name' => 'required',
            'editForm.slug' => 'required|unique:logo_tiendas,slug,' . $this->logo_tiendas->id,
        ];

        if ($this->editImage) {
            $rules['editImage'] = 'image|max:1024';
        }

        $this->validate($rules);

        if ($this->editImage) {
            Storage::delete($this->editForm['image']);

            $this->editForm['image'] = $this->editImage->store('logo_tiendas');
        }

        $this->banner->update($this->editForm);



        $this->reset(['editForm','editImage']);

        $this->getLogo_tiendas();
    }

    public function delete(Logotiends $logo_tienda){
        $logo_tienda->delete();
        $this->getLogo_tiendas();
    }

    public function render()
    {
        return view('livewire.admin.logo-tienda');
    }
}
