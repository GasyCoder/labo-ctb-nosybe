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
        // Table des prélèvements
        Schema::create('prelevements', function (Blueprint $table) {
            $table->id();
            $table->string('code')->comment('Code du prélèvement');
            $table->string('denomination')->nullable()->comment('Dénomination du prélèvement');
            $table->foreignId('type_tube_id')
                  ->nullable()
                  ->constrained('type_tubes')
                  ->onDelete('set null')
                  ->comment('Type de tube recommandé pour ce prélèvement');
            $table->decimal('prix', 10, 2)->comment('Prix du prélèvement');
            $table->integer('quantite')->default(1)->comment('Quantité disponible');
            $table->boolean('is_active')->default(true)->comment('Indique si le prélèvement est actif');
            $table->timestamps();
            $table->softDeletes();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prelevements');
    }
};
