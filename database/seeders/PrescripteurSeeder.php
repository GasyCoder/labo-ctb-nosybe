<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Prescripteur;

class PrescripteurSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prescripteurs = [
            [
                'nom' => 'RAKOTO',
                'prenom' => 'Jean',
                'grade' => 'Dr',
                'specialite' => 'Médecine Générale',
                'status' => 'Medecin', // ajouté
                'telephone' => '+261 32 12 345 67',
                'email' => 'dr.rakoto@gmail.com',
                'is_active' => true,
                'adresse' => '67 Avenue de l\'Indépendance',
                'ville' => 'Antananarivo',
                'code_postal' => '101',
                'notes' => 'Prescripteur régulier, spécialisé en médecine générale'
            ],
            [
                'nom' => 'RANDRIAMAHEFA',
                'prenom' => 'Marie',
                'grade' => 'Dr',
                'specialite' => 'Cardiologie',
                'status' => 'Medecin', // ajouté
                'telephone' => '+261 33 45 678 90',
                'email' => 'marie.randriamahefa@hotmail.com',
                'is_active' => true,
                'adresse' => '12 Rue de la Paix',
                'ville' => 'Fianarantsoa',
                'code_postal' => '301',
                'notes' => 'Cardiologue expérimentée'
            ],
            [
                'nom' => 'ANDRIANAIVORAVELONA',
                'prenom' => 'Paul',
                'grade' => 'Dr',
                'specialite' => 'Pédiatrie',
                'status' => 'Medecin', // ajouté
                'telephone' => '+261 34 56 789 01',
                'email' => 'paul.andriana@yahoo.fr',
                'is_active' => true,
                'adresse' => '89 Boulevard Joffre',
                'ville' => 'Toamasina',
                'code_postal' => '501',
                'notes' => 'Pédiatre réputé, travaille principalement avec les enfants'
            ],
            [
                'nom' => 'RASOANAIVO',
                'prenom' => 'Hanta',
                'grade' => 'Dr',
                'specialite' => 'Gynécologie',
                'status' => 'BiologieSolidaire', // ajouté (exemple pour varier)
                'telephone' => '+261 32 67 890 12',
                'email' => 'hanta.rasoanaivo@gmail.com',
                'is_active' => true,
                'adresse' => '45 Rue Rainandriamampandry',
                'ville' => 'Antananarivo',
                'code_postal' => '101',
                'notes' => 'Gynécologue-obstétricienne'
            ],
        ];

        foreach ($prescripteurs as $prescripteur) {
            Prescripteur::create($prescripteur);
        }

        $this->command->info(count($prescripteurs) . ' prescripteurs créés avec succès !');
    }
}
