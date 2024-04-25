<?php

namespace App\Http\Livewire\Admin;

use App\Http\Controllers\Admin\BannerController;
use App\Models\Banner;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class BannerImagen extends Component
{

    use WithFileUploads;
    public $banners, $banner, $rand;

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
        'createForm.slug' => 'required|unique:banners,slug',
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

        $this->getBanners();
        $this->rand = rand();
    }

    public function updatedCreateFormName($value){
        $this->createForm['slug'] = Str::slug($value);
    }

    public function updatedEditFormName($value){
        $this->editForm['slug'] = Str::slug($value);
    }


    public function getBanners(){
        $this->banners = Banner::all();
    }

    public function save(){

        $this->validate();

        $image = $this->createForm['image']->store('banners');

       $banner = Banner::create([
            'name' => $this->createForm['name'],
            'slug' => $this->createForm['slug'],
            'image' => $image
        ]);


        $this->rand = rand();
        $this->reset('createForm');

        $this->getBanners();
        $this->emit('saved');
    }

    public function edit(Banner $banner){

        $this->reset(['editImage']);

        $this->resetValidation();

        $this->banner = $banner;

        $this->editForm['open'] = true;
        $this->editForm['name'] = $banner->name;
        $this->editForm['slug'] = $banner->slug;
        $this->editForm['image'] = $banner->image;

    }

    public function update(){

        $rules = [
            'editForm.name' => 'required',
            'editForm.slug' => 'required|unique:banners,slug,' . $this->banner->id,
        ];

        if ($this->editImage) {
            $rules['editImage'] = 'image|max:1024';
        }

        $this->validate($rules);

        if ($this->editImage) {
            Storage::delete($this->editForm['image']);

            $this->editForm['image'] = $this->editImage->store('banners');
        }

        $this->banner->update($this->editForm);



        $this->reset(['editForm','editImage']);

        $this->getBanners();
    }

    public function delete(Banner $banner){
        $banner->delete();
        $this->getBanners();
    }

    public function render()
    {
        return view('livewire.admin.banner-imagen');
    }
}
