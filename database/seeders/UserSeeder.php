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
            'email' => 'admin@labo.com', // ✅ AJOUT
            'type' => 'admin',
            'password' => Hash::make('adminlabo'),
        ]);

        User::create([
            'name' => 'Secretaire Test',
            'username' => 'secretaire',
            'email' => 'secretaire@labo.com', // ✅ AJOUT
            'type' => 'secretaire',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Technicien Test',
            'username' => 'technicien',
            'email' => 'technicien@labo.com', // ✅ AJOUT
            'type' => 'technicien',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Biologiste Test',
            'username' => 'biologiste',
            'email' => 'biologiste@labo.com', // ✅ AJOUT
            'type' => 'biologiste',
            'password' => Hash::make('password'),
        ]);
    }
}