<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodePkl;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PeriodePklController extends Controller
{
    public function index(Request $request)
    {
        $query = PeriodePkl::query();

        if ($request->status === 'aktif') {
            $query->where('is_active', true);
        }

        if ($request->status === 'nonaktif') {
            $query->where('is_active', false);
        }

        $periode = $query->orderBy('tanggal_mulai', 'desc')->get();

        return view('dashboard.admin.periode.index', compact('periode'));
    }


    public function create()
    {
        $lastPeriode = PeriodePkl::orderBy('tanggal_selesai', 'desc')->first();

        $minDate = now()->format('Y-m-d');

        if ($lastPeriode) {
            $minDate = Carbon::parse($lastPeriode->tanggal_selesai)
                ->addDay()
                ->format('Y-m-d');
        }

        return view('dashboard.admin.periode.create', compact('minDate'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_periode' => 'required',
            'tahun_ajaran' => 'required|regex:/^\d{4}\/\d{4}$/',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'is_active' => 'required|boolean',
        ]);

        $overlap = PeriodePkl::where(function ($q) use ($data) {

            $q->whereBetween('tanggal_mulai', [$data['tanggal_mulai'], $data['tanggal_selesai']])
                ->orWhereBetween('tanggal_selesai', [$data['tanggal_mulai'], $data['tanggal_selesai']])
                ->orWhere(function ($q2) use ($data) {
                    $q2->where('tanggal_mulai', '<=', $data['tanggal_mulai'])
                        ->where('tanggal_selesai', '>=', $data['tanggal_selesai']);
                });

        })->exists();

        if ($overlap) {
            return back()
                ->withInput()
                ->withErrors([
                    'tanggal_mulai' => 'Tanggal periode bertabrakan dengan periode lain.'
                ]);
        }

        // jika periode baru aktif → nonaktifkan semua yang lain
        if ($data['is_active']) {
            PeriodePkl::where('is_active', true)->update([
                'is_active' => false
            ]);
        }

        PeriodePkl::create($data);

        return redirect()
            ->route('admin.periode.index')
            ->with('success', 'Periode PKL berhasil ditambahkan');
    }

    public function edit(PeriodePkl $periode)
    {
        $prevPeriode = PeriodePkl::where('tanggal_selesai', '<', $periode->tanggal_mulai)
            ->orderBy('tanggal_selesai', 'desc')
            ->first();

        $minDate = null;

        if ($prevPeriode) {
            $minDate = Carbon::parse($prevPeriode->tanggal_selesai)
                ->addDay()
                ->format('Y-m-d');
        }

        return view('dashboard.admin.periode.edit', compact('periode', 'minDate'));
    }

    public function update(Request $request, PeriodePkl $periode)
    {
        $data = $request->validate([
            'nama_periode' => 'required',
            'tahun_ajaran' => 'required|regex:/^\d{4}\/\d{4}$/',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'is_active' => 'required|boolean',
        ]);

        // ================= CEK PERIODE SEBELUMNYA =================
        $prevPeriode = PeriodePkl::where('id', '!=', $periode->id)
            ->where('tanggal_selesai', '<', $periode->tanggal_mulai)
            ->orderBy('tanggal_selesai', 'desc')
            ->first();

        if ($prevPeriode) {

            $minDate = Carbon::parse($prevPeriode->tanggal_selesai)->addDay();

            if (Carbon::parse($data['tanggal_mulai'])->lt($minDate)) {
                return back()->withInput()->withErrors([
                    'tanggal_mulai' => 'Tanggal tidak boleh sebelum periode sebelumnya selesai.'
                ]);
            }

        }

        // ================= CEK OVERLAP =================
        $overlap = PeriodePkl::where('id', '!=', $periode->id)
            ->where(function ($q) use ($data) {

                $q->whereBetween('tanggal_mulai', [$data['tanggal_mulai'], $data['tanggal_selesai']])
                    ->orWhereBetween('tanggal_selesai', [$data['tanggal_mulai'], $data['tanggal_selesai']])
                    ->orWhere(function ($q2) use ($data) {
                        $q2->where('tanggal_mulai', '<=', $data['tanggal_mulai'])
                            ->where('tanggal_selesai', '>=', $data['tanggal_selesai']);
                    });

            })->exists();

        if ($overlap) {
            return back()->withInput()->withErrors([
                'tanggal_mulai' => 'Tanggal periode bertabrakan dengan periode lain.'
            ]);
        }

        // ================= STATUS AKTIF =================
        if ($data['is_active']) {
            PeriodePkl::where('id', '!=', $periode->id)
                ->update(['is_active' => false]);
        }

        $periode->update($data);

        return redirect()
            ->route('admin.periode.index')
            ->with('success', 'Periode PKL berhasil diperbarui');
    }

    public function destroy(PeriodePkl $periode)
    {
        if ($periode->penempatanPkl()->exists()) {
            return back()->with('error', 'Periode tidak bisa dihapus karena sudah digunakan');
        }

        $periode->delete();

        return redirect()
            ->route('admin.periode.index')
            ->with('success', 'Periode berhasil dihapus');
    }
}
