<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tempat_pkl', function (Blueprint $table) {
            $table->time('jam_masuk')->after('radius_meter')->nullable();
            $table->integer('toleransi_keterlambatan')->after('jam_masuk')->default(0)
                ->comment('dalam menit');
            $table->json('hari_wajib')->after('toleransi_keterlambatan')->nullable();
        });
    }

    public function down()
    {
        Schema::table('tempat_pkl', function (Blueprint $table) {
            $table->dropColumn(['jam_masuk', 'toleransi_keterlambatan', 'hari_wajib']);
        });
    }

};
