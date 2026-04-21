<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('guru_pembimbing', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->unique()
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->string('nip', 20)->unique();
            $table->string('nama_lengkap', 100);
            $table->string('mata_pelajaran', 50)->nullable();
            $table->string('no_telepon', 15)->nullable();
            $table->string('foto_profil')->nullable();

            $table->timestamps();

            $table->index('nip');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru_pembimbing');
    }
};
