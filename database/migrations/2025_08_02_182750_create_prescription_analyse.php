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
        Schema::create('prescription_analyse', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained('prescriptions')->onDelete('cascade');
            $table->foreignId('analyse_id')->constrained('analyses')->onDelete('cascade');
            
            // ✅ Valeurs de référence (optionnelles)
            $table->string('valeur_min')->nullable();
            $table->string('valeur_max')->nullable();
            $table->string('valeur_normal')->nullable();
            $table->enum('status', [
                'EN_ATTENTE',    // Analyse attachée mais pas encore traitée
                'EN_COURS',      // Traitement en cours
                'TERMINE',       // Analyse terminée
                'VALIDE',        // Validée par le biologiste
                'A_REFAIRE',     // À refaire
                'ARCHIVE'        // Archivée
            ])->default('EN_ATTENTE');
            $table->timestamps();
            // ✅ Contrainte d'unicité
            $table->unique(['prescription_id', 'analyse_id'], 'unique_prescription_analyse');
            
            // ✅ Index pour les requêtes fréquentes
            $table->index(['prescription_id', 'status'], 'idx_prescription_status');
            $table->index(['analyse_id', 'status'], 'idx_analyse_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription_analyse'); // ✅ CORRECTION : était 'antibiotiques'
    }
};