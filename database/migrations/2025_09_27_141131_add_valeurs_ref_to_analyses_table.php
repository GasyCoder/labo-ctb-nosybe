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
        Schema::table('analyses', function (Blueprint $table) {
            $table->string('valeur_ref_homme')->nullable()->after('valeur_ref'); 
            $table->string('valeur_ref_femme')->nullable()->after('valeur_ref_homme');
            $table->string('valeur_ref_enfant_garcon')->nullable()->after('valeur_ref_femme');
            $table->string('valeur_ref_enfant_fille')->nullable()->after('valeur_ref_enfant_garcon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            $table->dropColumn([
                'valeur_ref_homme',
                'valeur_ref_femme',
                'valeur_ref_enfant_garcon',
                'valeur_ref_enfant_fille',
            ]);
        });
    }
};
