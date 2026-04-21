<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 🔥 1. Tambahkan dulu 'nonaktif' ke enum
        DB::statement("
            ALTER TABLE penempatan_pkl 
            MODIFY status ENUM('aktif','selesai','dibatalkan','nonaktif') 
            NOT NULL DEFAULT 'aktif'
        ");

        // 🔥 2. Baru update data lama
        DB::statement("
            UPDATE penempatan_pkl 
            SET status = 'nonaktif' 
            WHERE status IN ('selesai','dibatalkan')
        ");

        // 🔥 3. Baru hapus enum lama
        DB::statement("
            ALTER TABLE penempatan_pkl 
            MODIFY status ENUM('aktif','nonaktif') 
            NOT NULL DEFAULT 'nonaktif'
        ");
    }

    public function down(): void
    {
        // balik ke enum lama
        DB::statement("
            ALTER TABLE penempatan_pkl 
            MODIFY status ENUM('aktif','selesai','dibatalkan') 
            NOT NULL DEFAULT 'aktif'
        ");
    }
};