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
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();

            // Relasi ke users
            $table->foreignId('user_id')
                  ->unique()
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Data identitas siswa
            $table->string('nis', 20)->unique();
            $table->string('nama_lengkap', 100);
            $table->string('kelas', 20);
            $table->string('jurusan', 50);

            // Kontak & profil
            $table->string('no_telepon', 15)->nullable();
            $table->text('alamat')->nullable();
            $table->string('foto_profil')->nullable();

            $table->timestamps();

            // Index tambahan
            $table->index('nis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
