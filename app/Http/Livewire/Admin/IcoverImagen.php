<?php

namespace App\Http\Livewire\Admin;

use App\Models\Icover;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class IcoverImagen extends Component
{

    use WithFileUploads;
    public $icovers, $icover, $rand;

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
        'createForm.slug' => 'required|unique:icovers,slug',
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

        $this->getIcovers();
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

    public function getIcovers()
    {
        $this->icovers = icover::all();
    }

    public function save()
    {

        $this->validate();

        $image = $this->createForm['image']->store('icovers');

        $icover = icover::create([
            'name' => $this->createForm['name'],
            'slug' => $this->createForm['slug'],
            'image' => $image
        ]);


        $this->rand = rand();
        $this->reset('createForm');

        $this->getIcovers();
        $this->emit('saved');
    }

    public function edit(icover $icover)
    {

        $this->reset(['editImage']);

        $this->resetValidation();

        $this->icover = $icover;

        $this->editForm['open'] = true;
        $this->editForm['name'] = $icover->name;
        $this->editForm['slug'] = $icover->slug;
        $this->editForm['image'] = $icover->image;
    }

    public function update()
    {

        $rules = [
            'editForm.name' => 'required',
            'editForm.slug' => 'required|unique:icovers,slug,' . $this->icover->id,
        ];

        if ($this->editImage) {
            $rules['editImage'] = 'image|max:1024';
        }

        $this->validate($rules);

        if ($this->editImage) {
            Storage::delete($this->editForm['image']);

            $this->editForm['image'] = $this->editImage->store('icovers');
        }

        $this->icover->update($this->editForm);



        $this->reset(['editForm', 'editImage']);

        $this->getIcovers();
    }

    public function delete(icover $icover)
    {
        $icover->delete();
        $this->getIcovers();
    }
    public function render()
    {
        return view('livewire.admin.icover-imagen');
    }
}
