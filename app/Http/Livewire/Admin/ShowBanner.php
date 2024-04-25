<?php

namespace App\Http\Livewire\Admin;

use App\Models\Banner;

use Livewire\Component;

use Illuminate\Support\Str;

class ShowBanner extends Component
{
    public $banner;

    protected $listeners = ['delete'];

    protected $rules = [
        'createForm.name' => 'required',
        'createForm.slug' => 'required|unique:banner,slug',

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

    public function mount(Banner $banner){
        $this->banner = $banner;
        $this->getBanner();
    }

    public function updatedCreateFormName($value){
        $this->createForm['slug'] = Str::slug($value);
    }

    public function updatedEditFormName($value){
        $this->editForm['slug'] = Str::slug($value);
    }

    public function save(){
        $this->validate();

        $this->banner->banner()->create($this->createForm);
        $this->reset('createForm');
        $this->getBanners();
    }

    public function edit(Banner $banner){

        $this->resetValidation();

        $this->banner = $banner;

        $this->editForm['open'] = true;
        $this->editForm['name'] = $banner->name;
        $this->editForm['slug'] = $banner->slug;

    }

    public function update(){
        $this->validate([
            'editForm.name' => 'required',
            'editForm.slug' => 'required|unique:banners,slug,' . $this->banner->id,
        ]);

        $this->subcategory->update($this->editForm);
        $this->getBanners();
        $this->reset('editForm');

    }

    public function delete(Banner $banner){
        $banner->delete();
        $this->getBanners();
    }

    public function render()
    {
        return view('livewire.admin.show-banner')->layout('layouts.admin');
    }
}
