<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BacterieFamilleSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'name' => 'ENTEROBACTERIACEAE',
                'antibiotics' => [
                    'Ampicilline / Amoxicilline',
                    'Amoxicilline + acide clavulunique',
                    'C1G (Cefalotine,...)',
                    'C3G (ceftriaxone, céfotaxime, céfixime)',
                    'Céfopérazone',
                    'Phénicolés (Tiamphénicol/Chloramphénicol)',
                    'Ciprofloxacine / Ofloxacine',
                    'Gentamicine',
                    'Impénème',
                    'Levofloxacine',
                    'Amikacine',
                    'Céfoxitine',
                    'Triméthoprime sulphamétozazole (Bactrim,...)',
                    'C4G (céfépime)'
                ],
                'bacteries' => [
                    'Escherichia coli',
                    'Proteus mirabilis',
                    'Proteus vulgarus',
                    'Klebsiella oxytoca',
                    'Salmonella Typhi',
                    'Shigella sp',
                    'Salmonella sp',
                    'Serratia sp',
                    'Citrobacter sp',
                    'Enterobacter cloacae',
                    'Klebsiella sp',
                    'Enterobacter sp',
                    'Klebsiella pneumoniae',
                    'Entérobactérie'
                ]
            ],
            [
                'name' => 'STAPHYLOCOCCUS',
                'antibiotics' => [
                    'Penicilline G',
                    'Oxacilline',
                    'Ciprofloxacine / Ofloxacine',
                    'Gentamicine',
                    'Clindamycine',
                    'Thiamphénicol/ chloramphénicol',
                    'Amoxicilline-acide clavulanique',
                    'Érythromycine',
                    'Céfoxitine (Interprétation valable pour Oxacilline)',
                    'Vancomycine',
                    'Rifampicine'
                ],
                'bacteries' => [
                    'Staphylococcus aureus',
                    'Staphylococcus à coagulase négative',
                    'autre germe',
                    'S. non aureus',
                    'Staphylococcus sp'
                ]
            ],
            [
                'name' => 'PSEUDOMONAS',
                'antibiotics' => [
                    'Ticarcilline',
                    'Ceftazidime (Fortum, ...)',
                    'Céfépime',
                    'Impénème',
                    'Pipéracilline',
                    'Ciprofloxacine',
                    'Lévofloxacine',
                    'Amikacine',
                    'Gentamicine',
                    'Ticarcilline + acide clavulanic (Claventin,...)'
                ],
                'bacteries' => [
                    'Pseudomonas aeruginosa',
                    'Stenotrophomonas maltophilia'
                ]
            ],
            [
                'name' => 'Bacilles à Gram positif',
                'antibiotics' => [
                    'Pénicilline G',
                    'Erythromycine',
                    'Ciprofloxacine',
                    'Gentamicine',
                    'Vancomycine',
                    'Triméthoprime-sulphamétozazole (Bactrim,...)'
                ],
                'bacteries' => [
                    'Bacilles de Doderleïn',
                    'Corynebacterium sp'
                ]
            ],
            [
                'name' => 'Streptococacceae',
                'antibiotics' => [
                    'Amoxicilline',
                    'Amoxicilline-acide clavulanique',
                    'Pénicilline G',
                    'Ceftriaxone',
                    'Erythromycine',
                    'Triméthoprime sulphamétozazole (Bactrim,...)',
                    'Chloramphénicol/ Thiamphénicol',
                    'Oxacilline',
                    'Levofloxacine'
                ],
                'bacteries' => [
                    'Streptococcus sp.',
                    'Streptococcus pneumoniae',
                    'Streptococcus agalactiae',
                    'Streptococcus pyogenes',
                    'Streptococcus alpha-hémolytique',
                    'Streptococcus beta-hémolytique',
                    'Enterococcus sp'
                ]
            ]
        ];

        foreach ($data as $famille) {
            // Insérer la famille
            $familleId = DB::table('bacterie_familles')->insertGetId([
                'designation' => $famille['name'],
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insérer les bactéries
            foreach ($famille['bacteries'] as $bacterie) {
                DB::table('bacteries')->insert([
                    'famille_id' => $familleId,
                    'designation' => $bacterie,
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Insérer les antibiotiques
            foreach ($famille['antibiotics'] as $antibiotique) {
                DB::table('antibiotiques')->insert([
                    'famille_id' => $familleId,
                    'designation' => $antibiotique,
                    'commentaire' => null,
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}