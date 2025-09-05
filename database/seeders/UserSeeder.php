<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Désactiver temporairement les contraintes de clés étrangères
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Supprimer les utilisateurs existants pour éviter les doublons
        User::truncate();
        
        // Réactiver les contraintes de clés étrangères
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        User::create([
            'name' => 'Administrateur Principal',
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

        // Créer quelques utilisateurs supplémentaires pour les tests
        User::create([
            'name' => 'Marie Dupont',
            'username' => 'mdupont',
            'type' => 'secretaire',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Jean Martin',
            'username' => 'jmartin',
            'type' => 'technicien',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Dr. Sarah Wilson',
            'username' => 'swilson',
            'type' => 'biologiste',
            'password' => Hash::make('password'),
        ]);
        
        echo "Utilisateurs créés avec succès!\n";
        echo "Admin: adminlabo / adminlabo\n";
        echo "Autres: secretaire, technicien, biologiste / password\n";
    }
}