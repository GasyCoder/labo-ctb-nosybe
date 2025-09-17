<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrelevementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. S'assurer que les types de tubes existent d'abord
        $this->createTypeTubesIfNotExists();
        
        // 2. Récupérer les IDs des types de tubes
        $tubeRouge = DB::table('type_tubes')->where('code', 'SEC')->first()?->id;
        $tubeEcouvillon = DB::table('type_tubes')->where('code', 'ECOUVILLON')->first()?->id;

        $prelevements = [
            [
                'code' => 'PL1',
                'denomination' => 'Prélèvement avec écouvillon stérile',
                'prix' => 15.00,
                'quantite' => 1,
                'is_active' => true,
                'type_tube_id' => $tubeEcouvillon, // Écouvillon pour prélèvement stérile
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'PL2',
                'denomination' => 'Prélèvement sanguin',
                'prix' => 25.00,
                'quantite' => 1,
                'is_active' => true,
                'type_tube_id' => $tubeRouge, // Tube rouge SEC pour sang standard
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'PL3',
                'denomination' => 'Prélèvement sanguin avec HGPO',
                'prix' => 35.00,
                'quantite' => 1,
                'is_active' => true,
                'type_tube_id' => $tubeRouge, // Tube rouge SEC pour HGPO
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'PL4',
                'denomination' => 'Prélèvement sanguin avec G50',
                'prix' => 30.00,
                'quantite' => 1,
                'is_active' => true,
                'type_tube_id' => $tubeRouge, // Tube rouge SEC pour G50
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('prelevements')->insert($prelevements);
        
        $this->command->info('✅ Prélèvements créés avec types de tubes recommandés');
        $this->command->info('PL1 → Écouvillon | PL2,PL3,PL4 → Tube Rouge (SEC)');
    }
    
    /**
     * Créer les types de tubes de base si ils n'existent pas
     */
    private function createTypeTubesIfNotExists()
    {
        $typeTubes = [
            ['code' => 'SEC', 'couleur' => 'Rouge'],
            ['code' => 'CITR', 'couleur' => 'Bleu'],
            ['code' => 'EDTA', 'couleur' => 'Violet'],
            ['code' => 'HEPA', 'couleur' => 'Vert'],
            ['code' => 'FLACON', 'couleur' => 'Transparent'],
            ['code' => 'ECOUVILLON', 'couleur' => 'Blanc'],
        ];

        foreach ($typeTubes as $type) {
            DB::table('type_tubes')->insertOrIgnore([
                'code' => $type['code'],
                'couleur' => $type['couleur'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}