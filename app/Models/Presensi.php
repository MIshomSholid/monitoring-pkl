<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PenempatanPkl;
use Carbon\Carbon;

class Presensi extends Model
{
    protected $table = 'presensi';

    protected $fillable = [
        'penempatan_pkl_id',
        'tanggal',
        'jenis_presensi',
        'waktu_presensi',
        'foto_presensi',
        'latitude',
        'longitude',
        'jarak_meter',
        'keterangan_izin',
        'bukti_izin',
        'status_validasi',
        'alasan_penolakan',
        'validated_by',
        'validated_at',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'validated_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
        'jarak_meter' => 'float',
    ];

    /* ================= RELATIONS ================= */

    // Relasi ke penempatan PKL
    public function penempatanPkl()
    {
        return $this->belongsTo(PenempatanPkl::class, 'penempatan_pkl_id');
    }

    public function siswa()
    {
        return $this->hasOneThrough(
            Siswa::class,
            PenempatanPkl::class,
            'id', // foreign key penempatan
            'id', // foreign key siswa
            'penempatan_pkl_id',
            'siswa_id'
        );
    }

    public function tempatPkl()
    {
        return $this->hasOneThrough(
            TempatPkl::class,
            PenempatanPkl::class,
            'id',
            'id',
            'penempatan_pkl_id',
            'tempat_pkl_id'
        );
    }

    // User (guru / pembimbing) yang memvalidasi
    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /* ================= HELPER ================= */

    public function isHadir()
    {
        return $this->jenis_presensi === 'hadir';
    }

    public function isIzin()
    {
        return $this->jenis_presensi === 'izin';
    }

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

    public function isTerlambat()
    {
        if ($this->jenis_presensi !== 'hadir') {
            return false;
        }

        $tempat = $this->penempatanPkl?->tempatPkl;

        if (!$tempat || !$tempat->jam_masuk) {
            return false;
        }

        $jamMasuk = Carbon::createFromFormat('H:i:s', $tempat->jam_masuk);
        $toleransi = (int) ($tempat->toleransi_keterlambatan ?? 0);

        $batasMasuk = $jamMasuk->addMinutes($toleransi);
        $jamPresensi = Carbon::createFromFormat('H:i:s', $this->waktu_presensi);

        return $jamPresensi->gt($batasMasuk);
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

    public function getFileBuktiAttribute()
    {
        if ($this->jenis_presensi === 'hadir') {
            return $this->foto_presensi;
        }

        if ($this->jenis_presensi === 'izin') {
            return $this->bukti_izin;
        }

        return null;
    }

    public function getJenisPresensiAkademikAttribute()
    {
        if (
            $this->status_validasi === 'ditolak' &&
            in_array($this->jenis_presensi, ['hadir', 'izin', 'libur'])
        ) {
            return 'alpha';
        }

        return $this->jenis_presensi;
    }
}
