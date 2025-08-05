<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('antibiotiques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('famille_id')->constrained('bacterie_familles');
            $table->string('designation');
            $table->text('commentaire')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes(); // Ajout de la colonne deleted_at pour le Soft Delete
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antibiotiques');
    }
};