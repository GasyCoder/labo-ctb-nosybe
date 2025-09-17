<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeTubesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $typeTubes = [
            [
                'code' => 'SEC',
                'couleur' => 'Rouge',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'CITR',
                'couleur' => 'Bleu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'EDTA',
                'couleur' => 'Violet',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'HEPA',
                'couleur' => 'Vert',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'FLACON',
                'couleur' => 'Transparent',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'ECOUVILLON',
                'couleur' => 'Blanc',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('type_tubes')->insert($typeTubes);
    }
}