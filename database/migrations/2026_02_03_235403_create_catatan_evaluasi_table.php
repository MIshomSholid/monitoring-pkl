<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('catatan_evaluasi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('penempatan_pkl_id')
                  ->constrained('penempatan_pkl')
                  ->cascadeOnDelete();

            $table->enum('kategori', [
                'pembimbing_lapangan',
                'guru_pembimbing'
            ]);

            $table->text('catatan');
            $table->date('tanggal');

            $table->foreignId('created_by')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->timestamps();

            $table->index('kategori');
            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catatan_evaluasi');
    }
};
