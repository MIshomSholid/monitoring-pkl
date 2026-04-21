<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tempat_pkl', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perusahaan', 150);
            $table->text('alamat');
            $table->string('no_telepon', 15)->nullable();
            $table->string('email', 100)->nullable();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->integer('radius_meter')->default(300);
            $table->integer('kuota_siswa')->default(1);
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            $table->index('nama_perusahaan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tempat_pkl');
    }
};
