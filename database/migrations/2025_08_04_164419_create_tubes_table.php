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
        Schema::create('tubes', function (Blueprint $table) {
            $table->id();
            
            // RELATIONS
            $table->foreignId('prescription_id')->constrained('prescriptions')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('prelevement_id')->constrained('prelevements')->onDelete('cascade');
            
            // IDENTIFICATION UNIQUE
            $table->string('code_barre')->unique()->comment('Code-barre unique du tube');
            $table->string('numero_tube')->comment('Numéro séquentiel lisible (ex: T-2024-001234)');
            
            // STATUT ET TRAÇABILITÉ
            $table->enum('statut', [
                'GENERE',        // Tube créé, code-barre imprimé
                'PRELEVE',       // Prélèvement effectué
                'RECEPTIONNE',   // Tube reçu au labo
                'EN_ANALYSE',    // Analyses en cours
                'ANALYSE_TERMINEE', // Analyses terminées
                'ARCHIVE',       // Tube archivé/détruit
                'PERDU',         // Tube perdu
                'REJETE'         // Tube rejeté (problème qualité)
            ])->default('GENERE');
            
            // INFORMATIONS TECHNIQUES
            $table->string('type_tube')->nullable()->comment('Type de tube (ex: EDTA, Héparine, Sec)');
            $table->decimal('volume_ml', 5, 2)->nullable()->comment('Volume en ml');
            $table->string('couleur_bouchon')->nullable()->comment('Couleur du bouchon');
            
            // TRAÇABILITÉ TEMPORELLE
            $table->timestamp('genere_at')->nullable();
            $table->timestamp('preleve_at')->nullable();
            $table->timestamp('receptionne_at')->nullable();
            $table->timestamp('archive_at')->nullable();
            
            // TRAÇABILITÉ UTILISATEUR
            $table->foreignId('preleve_par')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('receptionne_par')->nullable()->constrained('users')->onDelete('set null');
            
            // MÉTADONNÉES
            $table->text('observations')->nullable();
            $table->json('metadata')->nullable()->comment('Données techniques supplémentaires');
            
            $table->timestamps();
            $table->softDeletes();
            
            // INDEX pour performances
            $table->index(['prescription_id', 'statut']);
            $table->index(['patient_id', 'created_at']);
            $table->index('code_barre'); // Déjà unique mais index explicite
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tubes');
    }
};
