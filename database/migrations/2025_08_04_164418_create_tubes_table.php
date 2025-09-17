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
            $table->string('code_barre')->nullable()->unique()->comment('Code-barre unique du tube');
            $table->foreignId('receptionne_par')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            // INDEX pour performances
            $table->index(['prescription_id']);
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
