<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Banners extends Component
{

    public $banners;


    public function loadPosts(){

        $this->emit('glider', $this->banner->id, $this->banner->image);

    }

    public function render()
    {
        return view('livewire.banners');
    }
}
