<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePembimbingLapanganTable extends Migration
{
    public function up(): void
    {
        Schema::create('pembimbing_lapangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->unique()
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->string('nama_lengkap', 100);
            $table->string('jabatan', 50)->nullable();
            $table->string('no_telepon', 15)->nullable();
            $table->string('email_perusahaan', 100)->nullable();
            $table->string('foto_profil')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembimbing_lapangan');
    }
}
