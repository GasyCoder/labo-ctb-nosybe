<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('prescripteurs', function (Blueprint $table) {
            $table->id();
            $table->string('grade')->nullable();
            $table->string('nom');
            $table->string('prenom')->nullable();
            $table->enum('status', ['Medecin', 'BiologieSolidaire'])->default('Medecin');
            $table->string('specialite')->nullable(); // retiré ->after('nom')
            $table->string('specialite')->nullable(); // garder une seule fois ici
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('adresse')->nullable();
            $table->string('ville')->nullable();
            $table->string('code_postal')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index(['is_active']);
            $table->index(['nom', 'prenom']);
            $table->index(['status']);
            $table->index(['email']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('prescripteurs');
    }
};
