<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('penempatan_pkl', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('siswa_id');
            $table->unsignedBigInteger('tempat_pkl_id');
            $table->unsignedBigInteger('periode_pkl_id');
            $table->unsignedBigInteger('guru_pembimbing_id');
            $table->unsignedBigInteger('pembimbing_lapangan_id');

            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');

            $table->enum('status', ['aktif', 'selesai', 'dibatalkan'])
                  ->default('aktif');

            $table->timestamps();

            // Foreign Key
            $table->foreign('siswa_id')->references('id')->on('siswa')->cascadeOnDelete();
            $table->foreign('tempat_pkl_id')->references('id')->on('tempat_pkl')->cascadeOnDelete();
            $table->foreign('periode_pkl_id')->references('id')->on('periode_pkl')->cascadeOnDelete();
            $table->foreign('guru_pembimbing_id')->references('id')->on('guru_pembimbing')->cascadeOnDelete();
            $table->foreign('pembimbing_lapangan_id')->references('id')->on('pembimbing_lapangan')->cascadeOnDelete();

            // Index & Constraint
            $table->index('siswa_id');
            $table->index('status');
            $table->unique(['siswa_id', 'periode_pkl_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penempatan_pkl');
    }
};
