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
        Schema::create('resultat_antibiotiques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('antibiogramme_id')->constrained('antibiogrammes')->onDelete('cascade');
            $table->foreignId('antibiotique_id')->constrained('antibiotiques')->onDelete('cascade');
            $table->enum('interpretation', ['S', 'I', 'R']);
            $table->decimal('diametre_mm', 5, 2)->nullable();
            $table->timestamps();
            
            $table->unique(['antibiogramme_id', 'antibiotique_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resultat_antibiotiques');
    }
};
