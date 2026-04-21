<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanAkhirPkl extends Model
{
    protected $table = 'laporan_akhir_pkl';

    protected $fillable = [
        'penempatan_pkl_id',
        'status_validasi',
        'catatan_validasi',
        'validated_by',
        'validated_at'
    ];

    public function penempatan()
    {
        return $this->belongsTo(PenempatanPkl::class, 'penempatan_pkl_id');
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
