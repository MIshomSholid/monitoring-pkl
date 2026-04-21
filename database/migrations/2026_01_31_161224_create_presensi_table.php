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
        Schema::create('presensi', function (Blueprint $table) {
            $table->id();

            // Relasi ke penempatan PKL
            $table->foreignId('penempatan_pkl_id')
                ->constrained('penempatan_pkl')
                ->cascadeOnDelete();

            // Data utama presensi
            $table->date('tanggal');
            $table->enum('jenis_presensi', ['hadir', 'izin']);
            $table->time('waktu_presensi');

            // Presensi hadir
            $table->string('foto_presensi')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('jarak_meter', 10, 2)->nullable();

            // Presensi izin
            $table->text('keterangan_izin')->nullable();
            $table->string('bukti_izin')->nullable();

            // Validasi pembimbing
            $table->enum('status_validasi', ['menunggu', 'diterima', 'ditolak'])
                ->default('menunggu');
            $table->text('alasan_penolakan')->nullable();

            $table->foreignId('validated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('validated_at')->nullable();

            $table->timestamps();

            // Index
            $table->index('tanggal');
            $table->index('status_validasi');

            // 1x presensi per hari
            $table->unique(['penempatan_pkl_id', 'tanggal'], 'unique_presensi_harian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
};
