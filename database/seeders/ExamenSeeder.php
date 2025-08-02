<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExamenSeeder extends Seeder
{
    public function run(): void
    {
        $examens = [
            ['id' => 1, 'name' => 'BIOCHIMIE', 'abr' => 'BIO', 'status' => true],
            ['id' => 2, 'name' => 'HEMATOLOGIE', 'abr' => 'HEM', 'status' => true],
            ['id' => 3, 'name' => 'PARASITOLOGIE', 'abr' => 'PAR', 'status' => true],
            ['id' => 4, 'name' => 'SEROLOGIE (TECHNIQUE ELISA, TECHNIQUE IMMUNOCHROMATOGRAPHIQUE)', 'abr' => 'SER', 'status' => true],
            ['id' => 5, 'name' => 'BACTERIOLOGIE', 'abr' => 'BAC', 'status' => true],
            ['id' => 6, 'name' => 'IMMUNOLOGIE', 'abr' => 'IMM', 'status' => true],
            ['id' => 7, 'name' => 'HORMONOLOGIE', 'abr' => 'HOR', 'status' => true],
            ['id' => 8, 'name' => 'VIROLOGIE', 'abr' => 'VIR', 'status' => true],
        ];

        // Utilise upsert pour éviter les doublons en cas de plusieurs seeds
        DB::table('examens')->upsert($examens, ['id'], ['name', 'abr', 'status']);
    }
}
