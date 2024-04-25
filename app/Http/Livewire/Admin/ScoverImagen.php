<?php

namespace App\Http\Livewire\Admin;

use App\Models\Scover;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class ScoverImagen extends Component
{

    use WithFileUploads;
    public $scovers, $scover, $rand;

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
        'createForm.slug' => 'required|unique:scovers,slug',
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

        $this->getscovers();
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

    public function getScovers()
    {
        $this->scovers = scover::all();
    }

    public function save()
    {

        $this->validate();

        $image = $this->createForm['image']->store('scovers');

        $scover = scover::create([
            'name' => $this->createForm['name'],
            'slug' => $this->createForm['slug'],
            'image' => $image
        ]);


        $this->rand = rand();
        $this->reset('createForm');

        $this->getScovers();
        $this->emit('saved');
    }

    public function edit(scover $scover)
    {

        $this->reset(['editImage']);

        $this->resetValidation();

        $this->scover = $scover;

        $this->editForm['open'] = true;
        $this->editForm['name'] = $scover->name;
        $this->editForm['slug'] = $scover->slug;
        $this->editForm['image'] = $scover->image;
    }

    public function update()
    {

        $rules = [
            'editForm.name' => 'required',
            'editForm.slug' => 'required|unique:scovers,slug,' . $this->scover->id,
        ];

        if ($this->editImage) {
            $rules['editImage'] = 'image|max:1024';
        }

        $this->validate($rules);

        if ($this->editImage) {
            Storage::delete($this->editForm['image']);

            $this->editForm['image'] = $this->editImage->store('scovers');
        }

        $this->scover->update($this->editForm);



        $this->reset(['editForm', 'editImage']);

        $this->getScovers();
    }

    public function delete(scover $scover)
    {
        $scover->delete();
        $this->getScovers();
    }
    public function render()
    {
        return view('livewire.admin.scover-imagen');
    }
}
