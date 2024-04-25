<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $role = Role::create(['name' => 'admin']);

        User::create([
            'name'=> 'admin',
            'email'=> 'admin@saco.com',
            'password'=> bcrypt('12345678')
        ])->assignRole('admin');
    }
}


