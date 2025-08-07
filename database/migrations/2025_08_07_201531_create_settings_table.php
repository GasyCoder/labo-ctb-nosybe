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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('nom_entreprise');
            $table->string('nif')->nullable(); 
            $table->string('statut')->nullable();
            $table->decimal('remise_pourcentage', 5, 2)->default(0);
            $table->boolean('activer_remise')->default(false);
            $table->enum('format_unite_argent', ['Ar', 'MGA', 'Ariary'])->default('Ar');
            $table->boolean('commission_prescripteur')->default(false);
            $table->decimal('commission_prescripteur_pourcentage', 5, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
