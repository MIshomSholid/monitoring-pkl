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
        /*
        |----------------------------------------------------------
        | USERS (AKUN LOGIN)
        |----------------------------------------------------------
        */
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // IDENTITAS LOGIN
            $table->string('username', 50)->unique();
            $table->string('email', 100)->unique();
            $table->string('password');

            // ROLE & STATUS
            $table->enum('role', [
                'admin',
                'guru_pembimbing',
                'pembimbing_lapangan',
                'siswa'
            ])->index();

            $table->boolean('is_active')->default(true);
            $table->dateTime('last_login')->nullable();

            // DEFAULT LARAVEL
            $table->rememberToken();
            $table->timestamps();
        });

        /*
        |----------------------------------------------------------
        | PASSWORD RESET TOKENS
        |----------------------------------------------------------
        */
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        /*
        |----------------------------------------------------------
        | SESSIONS
        |----------------------------------------------------------
        */
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
