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
        /**
         * STEP 1
         * Tambah kolom dalam kondisi NULLABLE
         * (wajib supaya data lama tidak error)
         */
        Schema::table('penempatan_pkl', function (Blueprint $table) {
            $table->date('tanggal_mulai')
                  ->nullable()
                  ->after('periode_pkl_id');

            $table->date('tanggal_selesai')
                  ->nullable()
                  ->after('tanggal_mulai');
        });

        /**
         * STEP 2
         * Isi data lama berdasarkan periode PKL
         * (fallback aman & logis)
         */
        DB::statement("
            UPDATE penempatan_pkl pp
            JOIN periode_pkl p ON p.id = pp.periode_pkl_id
            SET
                pp.tanggal_mulai   = p.tanggal_mulai,
                pp.tanggal_selesai = p.tanggal_selesai
            WHERE
                pp.tanggal_mulai IS NULL
                OR pp.tanggal_selesai IS NULL
        ");

        /**
         * STEP 3
         * Ubah menjadi NOT NULL setelah data aman
         */
        Schema::table('penempatan_pkl', function (Blueprint $table) {
            $table->date('tanggal_mulai')->nullable(false)->change();
            $table->date('tanggal_selesai')->nullable(false)->change();

            $table->index(
                ['tanggal_mulai', 'tanggal_selesai'],
                'idx_masa_pkl'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penempatan_pkl', function (Blueprint $table) {
            $table->dropIndex('idx_masa_pkl');
            $table->dropColumn(['tanggal_mulai', 'tanggal_selesai']);
        });
    }
};
