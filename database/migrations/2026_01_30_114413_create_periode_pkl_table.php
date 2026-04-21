<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('periode_pkl', function (Blueprint $table) {
            $table->id();

            $table->string('nama_periode', 100);
            $table->string('tahun_ajaran', 20);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');

            $table->boolean('is_active')->default(true);
            $table->text('keterangan')->nullable();

            $table->timestamps();

            // Index untuk performa & filtering
            $table->index('tahun_ajaran');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periode_pkl');
    }
};
