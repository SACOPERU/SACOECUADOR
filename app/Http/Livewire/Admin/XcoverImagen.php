<?php

namespace App\Http\Livewire\Admin;

use App\Models\Xcover;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class XcoverImagen extends Component
{

    use WithFileUploads;
    public $xcovers, $xcover, $rand;

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
        'createForm.slug' => 'required|unique:xcovers,slug',
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

        $this->getXcovers();
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

    public function getXcovers()
    {
        $this->xcovers = Xcover::all();
    }

    public function save()
    {

        $this->validate();

        $image = $this->createForm['image']->store('xcovers');

        $xcover = Xcover::create([
            'name' => $this->createForm['name'],
            'slug' => $this->createForm['slug'],
            'image' => $image
        ]);


        $this->rand = rand();
        $this->reset('createForm');

        $this->getXcovers();
        $this->emit('saved');
    }

    public function edit(Xcover $xcover)
    {

        $this->reset(['editImage']);

        $this->resetValidation();

        $this->xcover = $xcover;

        $this->editForm['open'] = true;
        $this->editForm['name'] = $xcover->name;
        $this->editForm['slug'] = $xcover->slug;
        $this->editForm['image'] = $xcover->image;
    }

    public function update()
    {

        $rules = [
            'editForm.name' => 'required',
            'editForm.slug' => 'required|unique:xcovers,slug,' . $this->xcover->id,
        ];

        if ($this->editImage) {
            $rules['editImage'] = 'image|max:1024';
        }

        $this->validate($rules);

        if ($this->editImage) {
            Storage::delete($this->editForm['image']);

            $this->editForm['image'] = $this->editImage->store('xcovers');
        }

        $this->banner->update($this->editForm);



        $this->reset(['editForm', 'editImage']);

        $this->getBanners();
    }

    public function delete(Xcover $xcover)
    {
        $xcover->delete();
        $this->getBanners();
    }
    public function render()
    {
        return view('livewire.admin.xcover-imagen');
    }
}
