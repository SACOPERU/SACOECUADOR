<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Promocions extends Component
{

    public $promocions;


    public function loadPosts(){

        $this->emit('glider', $this->promocion->id, $this->promocion->image);

    }

    public function render()
    {
        return view('livewire.promocions');
    }
}
