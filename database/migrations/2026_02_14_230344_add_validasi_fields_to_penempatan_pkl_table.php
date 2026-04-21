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
        Schema::table('penempatan_pkl', function (Blueprint $table) {

            $table->enum('status_validasi', ['menunggu', 'diterima', 'ditolak'])
                  ->default('menunggu')
                  ->after('status');

            $table->text('catatan_validasi')
                  ->nullable()
                  ->after('status_validasi');

            $table->unsignedBigInteger('validated_by')
                  ->nullable()
                  ->after('catatan_validasi');

            $table->dateTime('validated_at')
                  ->nullable()
                  ->after('validated_by');

            // Foreign Key ke users
            $table->foreign('validated_by')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penempatan_pkl', function (Blueprint $table) {

            $table->dropForeign(['validated_by']);

            $table->dropColumn([
                'status_validasi',
                'catatan_validasi',
                'validated_by',
                'validated_at'
            ]);
        });
    }
};
