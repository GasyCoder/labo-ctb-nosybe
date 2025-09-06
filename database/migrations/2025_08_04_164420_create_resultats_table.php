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
        Schema::create('resultats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained('prescriptions')->onDelete('cascade');
            $table->foreignId('analyse_id')->constrained('analyses')->onDelete('cascade');
            
            // Données de résultat
            $table->text('resultats')->nullable()->comment('Résultats sous forme de texte ou JSON');
            $table->text('valeur')->nullable()->comment('Valeur numérique ou texte simple');
            $table->enum('interpretation', ['NORMAL', 'PATHOLOGIQUE'])->nullable();
            $table->text('conclusion')->nullable();
            
            // Statut du résultat
            $table->enum('status', [
                'EN_ATTENTE',    // Résultat non saisi
                'EN_COURS',      // Saisie/validation en cours
                'TERMINE',       // Résultat saisi, non validé
                'VALIDE',        // Résultat validé par le biologiste
                'A_REFAIRE',     // Résultat à refaire
                'ARCHIVE',       // Résultat archivé
            ])->default('EN_ATTENTE');
            
            // Relations optionnelles
            $table->foreignId('tube_id')->nullable()->constrained('tubes')->onDelete('set null');
            $table->foreignId('famille_id')->nullable()->constrained('bacterie_familles')->onDelete('set null');
            $table->foreignId('bacterie_id')->nullable()->constrained('bacteries')->onDelete('set null');
            
            // Validation
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('validated_at')->nullable();

            $table->softDeletes();
            $table->timestamps();
            
            // CONTRAINTE D'INTÉGRITÉ - Un seul résultat par analyse par prescription
            $table->unique(['prescription_id', 'analyse_id'], 'unique_prescription_analyse_resultat');
            
            // Index pour performances
            $table->index(['prescription_id', 'status'], 'idx_prescription_resultat_status');
            $table->index(['analyse_id', 'status'], 'idx_analyse_resultat_status');
            $table->index('status', 'idx_resultat_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resultats');
    }
};