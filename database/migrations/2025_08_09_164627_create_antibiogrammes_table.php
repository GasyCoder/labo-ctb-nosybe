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
        Schema::create('antibiogrammes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained('prescriptions')->onDelete('cascade');
            $table->foreignId('analyse_id')->constrained('analyses')->onDelete('cascade');
            $table->foreignId('bacterie_id')->constrained('bacteries')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['prescription_id', 'analyse_id', 'bacterie_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antibiogrammes');
    }
};
