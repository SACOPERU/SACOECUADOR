<?php

namespace App\Http\Livewire\Admin;

use App\Models\Vcover;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class VcoverImagen extends Component
{

    use WithFileUploads;
    public $vcovers, $vcover, $rand;

    protected $listeners = ['delete'];

    public $createForm = [

        'name' => null,
        'slug' => null,
        'image' => null,

    ];

    public $editForm = [
        'open' => false,
        'name' => null,
        'slug' => null,
        'image' => null,

    ];

    public $editImage;

    protected $rules = [
        'createForm.name' => 'required',
        'createForm.slug' => 'required|unique:vcovers,slug',
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

    public function mount()
    {

        $this->getVcovers();
        $this->rand = rand();
    }

    public function updatedCreateFormName($value)
    {
        $this->createForm['slug'] = Str::slug($value);
    }

    public function updatedEditFormName($value)
    {
        $this->editForm['slug'] = Str::slug($value);
    }

    public function getVcovers()
    {
        $this->vcovers = Vcover::all();
    }

    public function save()
    {

        $this->validate();

        $image = $this->createForm['image']->store('vcovers');

        $vcover = vcover::create([
            'name' => $this->createForm['name'],
            'slug' => $this->createForm['slug'],
            'image' => $image
        ]);


        $this->rand = rand();
        $this->reset('createForm');

        $this->getVcovers();
        $this->emit('saved');
    }

    public function edit(Vcover $vcover)
    {

        $this->reset(['editImage']);

        $this->resetValidation();

        $this->vcover = $vcover;

        $this->editForm['open'] = true;
        $this->editForm['name'] = $vcover->name;
        $this->editForm['slug'] = $vcover->slug;
        $this->editForm['image'] = $vcover->image;
    }

    public function update()
    {

        $rules = [
            'editForm.name' => 'required',
            'editForm.slug' => 'required|unique:vcovers,slug,' . $this->vcover->id,
        ];

        if ($this->editImage) {
            $rules['editImage'] = 'image|max:1024';
        }

        $this->validate($rules);

        if ($this->editImage) {
            Storage::delete($this->editForm['image']);

            $this->editForm['image'] = $this->editImage->store('vcovers');
        }

        $this->vcover->update($this->editForm);



        $this->reset(['editForm', 'editImage']);

        $this->getVcovers();
    }

    public function delete(Vcover $vcover)
    {
        $vcover->delete();
        $this->getVcovers();
    }
    public function render()
    {
        return view('livewire.admin.vcover-imagen');
    }
}
