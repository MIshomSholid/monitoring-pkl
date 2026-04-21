<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->unique(
                ['penempatan_pkl_id', 'tanggal'],
                'presensi_unique_per_hari'
            );
        });
    }

    public function down(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->dropUnique('presensi_unique_per_hari');
        });
    }
};
