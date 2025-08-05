<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('bacterie_antibiotique', function (Blueprint $table) {
            $table->id();
            $table->foreignId('antibiotique_id')->constrained()->onDelete('cascade');
            $table->foreignId('bacterie_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['antibiotique_id', 'bacterie_id']); // Évite les doublons
        });
    }

    public function down()
    {
        Schema::dropIfExists('bacterie_antibiotique');
    }
};