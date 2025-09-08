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
        Schema::create('bacterie_antibiotique', function (Blueprint $table) {
            $table->id();
            $table->foreignId('antibiotique_id')->constrained('antibiotiques')->onDelete('cascade');
            $table->foreignId('bacterie_id')->constrained('bacteries')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bacterie_antibiotique');
    }
};