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
        Schema::create('type_tubes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->unique()->comment('Code');
            $table->string('couleur')->nullable()->comment('Couleur du bouchon');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('type_tubes');
    }

};
