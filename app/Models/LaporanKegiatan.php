<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;


class LaporanKegiatan extends Model
{
    protected $table = 'laporan_kegiatan';

    protected $fillable = [
        'penempatan_pkl_id',
        'tanggal',
        'deskripsi_kegiatan',
        'file_bukti',
        'status_validasi',
        'catatan_validasi',
        'validated_by',
        'validated_at',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'validated_at' => 'datetime',
    ];

    // ================= RELATION =================

    public function penempatanPkl()
    {
        return $this->belongsTo(PenempatanPkl::class, 'penempatan_pkl_id');
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    // ================= HELPER =================

    public function isMenunggu()
    {
        return $this->status_validasi === 'menunggu';
    }

    public function isDiterima()
    {
        return $this->status_validasi === 'diterima';
    }

    public function isDitolak()
    {
        return $this->status_validasi === 'ditolak';
    }

    protected static function booted()
    {
        static::addGlobalScope('periode_aktif', function ($query) {
            $query->whereHas('penempatanPkl', function ($q) {
                $q->where('status', 'aktif')
                    ->whereHas('periode', function ($qq) {
                        $qq->where('is_active', true);
                    });
            });
        });
    }
}
