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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('numero_dossier')->unique();
            $table->string('nom');
            $table->string('prenom')->nullable();
            $table->string('civilite');
            $table->string('date_naissance')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->enum('sexe', ['M', 'F'])->nullable();
            $table->enum('statut', ['NOUVEAU', 'FIDELE', 'VIP'])->default('NOUVEAU');
            $table->timestamps();
            $table->softDeletes();

            $table->index('numero_dossier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
