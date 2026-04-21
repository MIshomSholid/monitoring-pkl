<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('penempatan_pkl', function (Blueprint $table) {
            $table->enum('status_pengajuan', ['aktif', 'nonaktif'])
                  ->nullable()
                  ->after('status');

            // default isi awal biar tidak null
        });

        // 🔥 isi data lama (biar aman)
        DB::table('penempatan_pkl')->update([
            'status_pengajuan' => DB::raw('status')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penempatan_pkl', function (Blueprint $table) {
            $table->dropColumn('status_pengajuan');
        });
    }
};