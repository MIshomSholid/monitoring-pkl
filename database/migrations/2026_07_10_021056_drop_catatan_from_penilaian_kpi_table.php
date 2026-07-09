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
        Schema::table('penilaian_kpi', function (Blueprint $table) {
            //Menghapus kolom catatan dari tabel penilaian_kpi
            $table->dropColumn('catatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penilaian_kpi', function (Blueprint $table) {
            //Menambahkan kembali kolom catatan ke tabel penilaian_kpi
            $table->text('catatan')->nullable()->after('periode_penilaian');
        });
    }
};
