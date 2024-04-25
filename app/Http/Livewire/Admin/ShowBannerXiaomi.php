<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\Bannerxiaomi;

class ShowBannerXiaomi extends Component
{
  public $bannerxiaomi;

    protected $listeners = ['delete'];
  
      protected $rules = [
        'createForm.name' => 'required',
        'createForm.slug' => 'required|unique:bannerxiaomi,slug',

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
  
      public function mount(Bannerxiaomi $bannerxiaomi){
        $this->bannerxiaomi = $bannerxiaomi;
        $this->getBannerxiaomi();
    }
  
   public function updatedCreateFormName($value){
        $this->createForm['slug'] = Str::slug($value);
    }

    public function updatedEditFormName($value){
        $this->editForm['slug'] = Str::slug($value);
    }
  
   public function save(){
        $this->validate();

        $this->bannerxiaomi->bannerxiaomi()->create($this->createForm);
        $this->reset('createForm');
        $this->getBannerxiaomi();
    }
  
  public function edit(Bannerxiaomi $bannerxiaomi){

        $this->resetValidation();

        $this->bannerxiaomi = $bannerxiaomi;

        $this->editForm['open'] = true;
        $this->editForm['name'] = $bannerxiaomi->name;
        $this->editForm['slug'] = $bannerxiaomi->slug;

    }
  
  public function update(){
        $this->validate([
            'editForm.name' => 'required',
            'editForm.slug' => 'required|unique:bannerxiaomi,slug,' . $this->bannerxiaomi->id,
        ]);

        
        $this->getBannerxiaomi();
        $this->reset('editForm');

    }
  
  public function delete(Bannerxiaomi $bannerxiaomi){
        $promocion->delete();
        $this->getBannerxiaomi();
    }
  
    public function render()
    {
        return view('livewire.admin.show-banner-xiaomi')->layout('layouts.admin');
    }
}
