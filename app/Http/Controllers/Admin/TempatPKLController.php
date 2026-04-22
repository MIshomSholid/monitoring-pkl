<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TempatPkl;
use Illuminate\Http\Request;

class TempatPKLController extends Controller
{
    public function index(Request $request)
    {
        $query = TempatPkl::query();

        // 🔍 SEARCH
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nama_perusahaan', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('no_telepon', 'like', "%{$search}%");
            });
        }

        $tempat = $query
            ->orderBy('nama_perusahaan')
            ->get();

        return view('dashboard.admin.tempat-pkl.index', compact('tempat'));
    }

    public function create()
    {
        return view('dashboard.admin.tempat-pkl.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_perusahaan' => 'required|string|max:150',
            'alamat' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius_meter' => 'required|integer|min:50',
            'kuota_siswa' => 'required|integer|min:1',
            'jam_masuk' => 'nullable|date_format:H:i',
            'toleransi_keterlambatan' => 'nullable|integer|min:0',
            'hari_wajib' => 'nullable|array',
            'hari_wajib.*' => 'in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
        ]);

        TempatPkl::create([
            'nama_perusahaan' => $request->nama_perusahaan,
            'alamat' => $request->alamat,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius_meter' => $request->radius_meter,
            'kuota_siswa' => $request->kuota_siswa,

            // SIMPAN SETTING PRESENSI
            'jam_masuk' => $request->jam_masuk,
            'toleransi_keterlambatan' => $request->toleransi_keterlambatan,
            'hari_wajib' => $request->hari_wajib,
        ]);

        return redirect()
            ->route('admin.tempat-pkl.index')
            ->with('success', 'Tempat PKL berhasil ditambahkan');
    }

    public function edit(TempatPkl $tempatPkl)
    {
        return view('dashboard.admin.tempat-pkl.edit', compact('tempatPkl'));
    }

    public function update(Request $request, TempatPkl $tempatPkl)
    {
        $request->validate([
            'nama_perusahaan' => 'required|string|max:150',
            'alamat' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius_meter' => 'required|integer|min:50',
            'kuota_siswa' => 'required|integer|min:1',
            'jam_masuk' => 'nullable|date_format:H:i',
            'toleransi_keterlambatan' => 'nullable|integer|min:0',
            'hari_wajib' => 'nullable|array',
            'hari_wajib.*' => 'in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
        ]);

        $tempatPkl->update([
        'nama_perusahaan' => $request->nama_perusahaan,
        'alamat' => $request->alamat,
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
        'radius_meter' => $request->radius_meter,
        'kuota_siswa' => $request->kuota_siswa,

        // ✅ UPDATE SETTING PRESENSI
        'jam_masuk' => $request->jam_masuk,
        'toleransi_keterlambatan' => $request->toleransi_keterlambatan,
        'hari_wajib' => $request->hari_wajib,
    ]);

        return redirect()
            ->route('admin.tempat-pkl.index')
            ->with('success', 'Tempat PKL berhasil diperbarui');
    }

    public function destroy(TempatPkl $tempatPkl)
    {
        $tempatPkl->delete();

        return back()->with('success', 'Tempat PKL dihapus');
    }
}
