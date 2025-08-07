<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::create([
            'nom_entreprise' => 'CTB NOSY BE',
            'nif' => '1234567890',
            'statut' => 'SARL',

            'remise_pourcentage' => 0,
            'activer_remise' => true,
            'unite_argent' => 'MGA',
            'commission_prescripteur' => true,
            'commission_prescripteur_pourcentage' => 10,
        ]);
    }
}
