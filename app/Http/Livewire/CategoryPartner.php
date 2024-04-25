<?php

namespace App\Http\Livewire;

use Livewire\Component;

class CategoryPartner extends Component
{	
  	public $category;


    public $products = [];

    public function loadPosts()
    {
        $this->products = $this->category->products()
            ->where(function ($query) {
                $query->where('status', 2)
                    ->orWhereNotNull('price_partner');
            })->take(8)->get();

        $this->emit('glider', $this->category->id);
    }
  
    public function render()
    {
        return view('livewire.category-partner');
    }
}
