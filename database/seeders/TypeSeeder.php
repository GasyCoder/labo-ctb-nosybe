<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Type;

class TypeSeeder extends Seeder
{
    public function run()
    {
        $analyseTypes = [
            ['id' => 1, 'name' => 'MULTIPLE', 'libelle' => 'Ensemble de plusieurs types élémentaires', 'status' => true],
            ['id' => 2, 'name' => 'TEST', 'libelle' => 'Test', 'status' => true],
            ['id' => 3, 'name' => 'CULTURE', 'libelle' => 'Culture', 'status' => true],
            ['id' => 4, 'name' => 'DOSAGE', 'libelle' => 'Dosage', 'status' => true],
            ['id' => 5, 'name' => 'COMPTAGE', 'libelle' => 'Comptage', 'status' => true],
            ['id' => 6, 'name' => 'MULTIPLE_SELECTIF', 'libelle' => 'Ensemble de plusieurs types élémentaires sélectifs', 'status' => true],
            ['id' => 7, 'name' => 'INPUT', 'libelle' => 'Champ libre', 'status' => true],
            ['id' => 8, 'name' => 'SELECT', 'libelle' => 'Champ de sélection', 'status' => true],
            ['id' => 9, 'name' => 'NEGATIF_POSITIF_1', 'libelle' => 'Négatif/Positif', 'status' => true],
            ['id' => 10, 'name' => 'NEGATIF_POSITIF_2', 'libelle' => 'Négatif/Positif + valeur de ref', 'status' => true],
            ['id' => 11, 'name' => 'NEGATIF_POSITIF_3', 'libelle' => 'Négatif/Positif + champ select multiple', 'status' => true],
            ['id' => 12, 'name' => 'INPUT_SUFFIXE', 'libelle' => 'Champ libre + suffixe', 'status' => true],
            ['id' => 13, 'name' => 'LEUCOCYTES', 'libelle' => 'Leucocytes', 'status' => true],
            ['id' => 14, 'name' => 'ABSENCE_PRESENCE_2', 'libelle' => 'Absence/Présence + valeur', 'status' => true],
            ['id' => 15, 'name' => 'GERME', 'libelle' => 'Germe isolé', 'status' => true],
            ['id' => 16, 'name' => 'LABEL', 'libelle' => 'Simple titre', 'status' => true],
            ['id' => 17, 'name' => 'SELECT_MULTIPLE', 'libelle' => 'Champ de sélection multiple valeur', 'status' => true],
            ['id' => 18, 'name' => 'FV', 'libelle' => 'Flore vaginale', 'status' => true],
        ];

        Type::upsert($analyseTypes, ['id'], ['name', 'libelle', 'status']);
    }
}
