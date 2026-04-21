<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_akhir_pkl', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('penempatan_pkl_id')->unique();

            $table->enum('status_validasi', [
                'menunggu',
                'diterima',
                'ditolak'
            ])->default('menunggu');

            $table->text('catatan_validasi')->nullable();

            $table->unsignedBigInteger('validated_by')->nullable();
            $table->timestamp('validated_at')->nullable();

            $table->timestamps();

            // =========================
            // FOREIGN KEY
            // =========================

            $table->foreign('penempatan_pkl_id')
                  ->references('id')
                  ->on('penempatan_pkl')
                  ->onDelete('cascade');

            $table->foreign('validated_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_akhir_pkl');
    }
};
