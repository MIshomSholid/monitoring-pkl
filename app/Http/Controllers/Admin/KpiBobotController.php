<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KpiKategori;
use App\Models\KpiIndikator;
use Illuminate\Support\Facades\DB;

class KpiBobotController extends Controller
{
    public function index()
    {
        $kategori = KpiKategori::with('indikator')->get();

        return view('dashboard.admin.kpi-bobot.index', compact('kategori'));
    }

    public function update(Request $request)
    {
        $kategoriInput = $request->kategori ?? [];

        // VALIDASI TOTAL BOBOT KATEGORI 
        $totalKategori = array_sum($kategoriInput);

        if ($totalKategori != 100) {
            return redirect()->back()->with(
                'error',
                'Total bobot kategori harus tepat 100%. Saat ini total = ' . $totalKategori . '%'
            );
        }

        DB::transaction(function () use ($request) {

            // UPDATE BOBOT KATEGORI
            if ($request->kategori) {
                foreach ($request->kategori as $id => $bobot) {
                    KpiKategori::where('id', $id)
                        ->update([
                            'bobot_persen' => $bobot
                        ]);
                }
            }

            // UPDATE BOBOT INDIKATOR (BELUM ADA VALIDASI)
            if ($request->indikator) {
                foreach ($request->indikator as $id => $bobot) {
                    KpiIndikator::where('id', $id)
                        ->update([
                            'bobot_persen' => $bobot
                        ]);
                }
            }
        });

        return redirect()->back()->with('success', 'Bobot KPI berhasil diperbarui');
    }
}