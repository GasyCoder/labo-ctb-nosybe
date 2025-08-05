<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrescripteurSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prescripteurs = [
            ['nom' => 'Dr. Andrianina Rakoto', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Dr. Razafindramahatra Fanja', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Dr. Heritiana Randrianarisoa', 'is_active' => false, 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Dr. Harisoa Rabearivony', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('prescripteurs')->insert($prescripteurs);
    }
}
