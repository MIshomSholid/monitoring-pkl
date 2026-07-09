<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PenempatanPkl;
use App\Models\KpiIndikator;

class PenilaianKpi extends Model
{
    protected $table = 'penilaian_kpi';

    protected $fillable = [
        'penempatan_pkl_id',
        'kpi_indikator_id',
        'nilai',
        'periode_penilaian',
        'status_validasi',
        'input_by',
        'validated_by',
        'validated_at',
    ];

    public function penempatanPkl()
    {
        return $this->belongsTo(PenempatanPkl::class, 'penempatan_pkl_id');
    }

    public function indikator()
    {
        return $this->belongsTo(KpiIndikator::class, 'kpi_indikator_id');
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
