<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('Code unique de la méthode (ex: ESPECES, CARTE)');
            $table->string('label', 100)->comment('Libellé affiché (ex: Espèces, Carte bancaire)');
            $table->boolean('is_active')->default(true)->comment('Méthode active ou non');
            $table->integer('display_order')->default(1)->comment('Ordre d\'affichage');
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index(['is_active', 'display_order']);
        });

        // Ajouter des données par défaut
        DB::table('payment_methods')->insert([
            [
                'code' => 'ESPECES',
                'label' => 'Espèces',
                'is_active' => true,
                'display_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'CARTE',
                'label' => 'Carte bancaire',
                'is_active' => true,
                'display_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'CHEQUE',
                'label' => 'Chèque',
                'is_active' => false,
                'display_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'MOBILEMONEY',
                'label' => 'Mobile Money',
                'is_active' => true,
                'display_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'VIREMENT',
                'label' => 'Virement bancaire',
                'is_active' => true,
                'display_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};