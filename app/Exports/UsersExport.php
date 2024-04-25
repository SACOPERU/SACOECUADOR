<?php

namespace App\Exports;

use App\User;
use Illuminate\Foundation\Auth\User as AuthUser;
use Maatwebsite\Excel\Concerns\FromCollection;

class UsersExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return AuthUser::all();
    }
}
