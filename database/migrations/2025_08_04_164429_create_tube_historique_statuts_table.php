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
        Schema::create('tube_historique_statuts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tube_id')->constrained('tubes')->onDelete('cascade');
            $table->string('ancien_statut');
            $table->string('nouveau_statut');
            $table->foreignId('modifie_par')->constrained('users')->onDelete('cascade');
            $table->text('commentaire')->nullable();
            $table->timestamp('modifie_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tube_historique_statuts');
    }
};
