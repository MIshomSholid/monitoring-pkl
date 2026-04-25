<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class PenempatanPkl extends Model
{
    protected $table = 'penempatan_pkl';

    protected $fillable = [
        'siswa_id',
        'tempat_pkl_id',
        'periode_pkl_id',
        'guru_pembimbing_id',
        'pembimbing_lapangan_id',
        'status_pengajuan',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_validasi',
        'validated_by',
        'validated_at',
    ];

    protected $casts = [
        // UNTUK <input type="date">
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    // ================= RELASI EXISTING (ADMIN) =================

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function tempat()
    {
        return $this->belongsTo(TempatPkl::class, 'tempat_pkl_id');
    }

    public function periode()
    {
        return $this->belongsTo(PeriodePkl::class, 'periode_pkl_id');
    }

    public function guru()
    {
        return $this->belongsTo(GuruPembimbing::class, 'guru_pembimbing_id');
    }

    public function pembimbingLapangan()
    {
        return $this->belongsTo(PembimbingLapangan::class, 'pembimbing_lapangan_id');
    }

    // ================= RELASI ALIAS (SISWA) =================
    // ⚠️ JANGAN HAPUS YANG DI ATAS

    public function tempatPkl()
    {
        return $this->tempat();
    }

    public function periodePkl()
    {
        return $this->periode();
    }

    public function guruPembimbing()
    {
        return $this->guru();
    }

    // ================= PRESENSI =================

    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'penempatan_pkl_id');
    }

    public function presensiHariIni()
    {
        return $this->hasOne(Presensi::class, 'penempatan_pkl_id')
            ->whereDate('tanggal', today());
    }

    // ================= HELPER =================

    public function isAktif()
    {
        return $this->status === 'aktif';
    }

    // 🔥 HELPER PALING PENTING (UNTUK PRESENSI)
    public function isAktifHariIni(): bool
    {
        $today = Carbon::today();

        return $this->isAktif()
            && $today->gte($this->tanggal_mulai)
            && $today->lt($this->tanggal_selesai);
    }

    // ================= LAPORAN & KPI =================

    public function laporanKegiatan()
    {
        return $this->hasMany(LaporanKegiatan::class, 'penempatan_pkl_id');
    }

    public function penilaianKpi()
    {
        return $this->hasMany(PenilaianKpi::class, 'penempatan_pkl_id');
    }

    public function catatanEvaluasi()
    {
        return $this->hasMany(CatatanEvaluasi::class, 'penempatan_pkl_id');
    }

    // ================= HELPER LAPORAN AKHIR =================

    public function totalHadir()
    {
        return $this->presensi()
            ->where('jenis_presensi', 'hadir')
            ->where('status_validasi', 'diterima')
            ->count();
    }

    public function totalIzin()
    {
        return $this->presensi()
            ->where('jenis_presensi', 'izin')
            ->where('status_validasi', 'diterima')
            ->count();
    }

    public function rataRataKpi()
    {
        return round(
            $this->penilaianKpi()
                ->where('status_validasi', 'diterima')
                ->avg('nilai'),
            2
        );
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function laporanAkhir()
    {
        return $this->hasOne(LaporanAkhirPkl::class, 'penempatan_pkl_id');
    }

    protected static function booted()
    {
        static::addGlobalScope('aktif', function (Builder $query) {
            $query->where('status', 'aktif')
                ->whereHas('periode', function ($q) {
                    $q->where('is_active', true);
                });
        });
    }

}
