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
        Schema::create('tube_analyse', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tube_id')->constrained('tubes')->onDelete('cascade');
            $table->foreignId('analyse_id')->constrained('analyses')->onDelete('cascade');
            
            // STATUT SPÉCIFIQUE À CETTE ANALYSE SUR CE TUBE
            $table->enum('statut_analyse', [
                'PLANIFIEE',     // Analyse planifiée
                'EN_COURS',      // En cours d'analyse
                'TERMINEE',      // Analyse terminée
                'VALIDEE',       // Validée par biologiste
                'A_REFAIRE',     // À refaire
                'IMPOSSIBLE'     // Impossible (tube détérioré, etc.)
            ])->default('PLANIFIEE');
            
            $table->timestamp('demarree_at')->nullable();
            $table->timestamp('terminee_at')->nullable();
            $table->timestamp('validee_at')->nullable();
            $table->foreignId('technicien_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('validee_par')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            // Contrainte d'unicité : un tube ne peut avoir qu'une fois la même analyse
            $table->unique(['tube_id', 'analyse_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tube_analyse');
    }
};
