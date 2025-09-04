<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->enum('type', ['secretaire', 'technicien', 'biologiste', 'admin']);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('username')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id', 255)->primary()->charset('utf8mb4')->collation('utf8mb4_bin'); // varchar(255) with proper charset/collation
            $table->unsignedBigInteger('user_id')->nullable()->index('sessions_user_id_index'); // bigint(20) unsigned + index
            $table->string('ip_address', 45)->nullable(); // varchar(45)
            $table->text('user_agent')->nullable(); // text
            $table->longText('payload'); // longtext
            $table->integer('last_activity')->index('sessions_last_activity_index'); // int(11) + index
        });

        // ✅ NOUVELLE TABLE AJOUTÉE
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('personal_access_tokens'); // ✅ AJOUTÉ AUSSI
    }
};