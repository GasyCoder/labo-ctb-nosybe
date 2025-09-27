<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('resultats', function (Blueprint $table) {
            $table->string('anteriorite')->nullable()->after('conclusion')
                  ->comment('Ancien résultat du patient pour comparaison');
            $table->date('anteriorite_date')->nullable()->after('anteriorite')
                  ->comment('Date de l\'ancien résultat');
            $table->unsignedBigInteger('anteriorite_prescription_id')->nullable()->after('anteriorite_date')
                  ->comment('ID de la prescription d\'origine de l\'antériorité');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('resultats', function (Blueprint $table) {
            $table->dropColumn(['anteriorite', 'anteriorite_date', 'anteriorite_prescription_id']);
        });
    }
};
