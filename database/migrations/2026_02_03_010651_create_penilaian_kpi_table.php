<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('penilaian_kpi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('penempatan_pkl_id')
                  ->constrained('penempatan_pkl')
                  ->cascadeOnDelete();

            $table->foreignId('kpi_indikator_id')
                  ->constrained('kpi_indikator')
                  ->cascadeOnDelete();

            $table->decimal('nilai', 5, 2);
            $table->date('periode_penilaian');
            $table->text('catatan')->nullable();

            $table->enum('status_validasi', ['menunggu', 'diterima', 'ditolak'])
                  ->default('menunggu');

            $table->foreignId('input_by')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('validated_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamp('validated_at')->nullable();

            $table->timestamps();

            $table->index('periode_penilaian');
            $table->index('status_validasi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_kpi');
    }
};
