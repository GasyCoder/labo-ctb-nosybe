<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'username' => 'adminlabo',
            'type' => 'admin',
            'password' => Hash::make('adminlabo'),
        ]);

        User::create([
            'name' => 'Secretaire Test',
            'username' => 'secretaire',
            'type' => 'secretaire',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Technicien Test',
            'username' => 'technicien',
            'type' => 'technicien',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Biologiste Test',
            'username' => 'biologiste',
            'type' => 'biologiste',
            'password' => Hash::make('password'),
        ]);
    }
}