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
            'email' => 'admin@test.com',
            'type' => 'admin', // OK
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Secretaire Test',
            'email' => 'secretaire@test.com',
            'type' => 'secretaire', // Doit exister dans l'enum !
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Technicien Test',
            'email' => 'technicien@test.com',
            'type' => 'technicien',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Biologiste Test',
            'email' => 'biologiste@test.com',
            'type' => 'biologiste',
            'password' => Hash::make('password'),
        ]);
    }
}
