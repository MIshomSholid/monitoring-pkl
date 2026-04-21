<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kpi_indikator', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_kategori_id')
                  ->constrained('kpi_kategori')
                  ->cascadeOnDelete();

            $table->string('nama_indikator', 100);
            $table->decimal('bobot_persen', 5, 2);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_indikator');
    }
};
