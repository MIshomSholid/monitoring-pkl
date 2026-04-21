<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PenempatanPkl;

class PeriodePkl extends Model
{
    protected $table = 'periode_pkl';

    protected $fillable = [
        'nama_periode',
        'tahun_ajaran',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_active',
        'keterangan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function penempatanPkl()
    {
        return $this->hasMany(PenempatanPkl::class, 'periode_pkl_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    public static function getAktif()
    {
        return self::aktif()->first();
    }
}
