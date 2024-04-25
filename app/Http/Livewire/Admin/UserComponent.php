<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\User;

use Livewire\WithPagination;

class UserComponent extends Component
{

    use WithPagination;
    public $search;

    public function updatingSearch(){
        $this->resertPage();
    }

    public function assingRole(User $user, $value){
        if ($value == '1') {
            $user->assignRole('admin');
        }else {
            $user->removeRole('admin');
        }
    }

    public function render()
    {

        $users = User::where('name', 'LIKE', '%' . $this->search . '%')
                    ->orwhere('email', 'LIKE', '%' . $this->search . '%')
                    ->paginate();

        return view('livewire.admin.user-component',compact('users'))->layout('layouts.admin');
    }
}
