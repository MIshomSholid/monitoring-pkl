<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PembimbingLapangan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PembimbingLapanganController extends Controller
{
    public function index(Request $request)
    {
        $query = PembimbingLapangan::with('user');

        // 🔍 SEARCH
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('jabatan', 'like', "%{$search}%")
                    ->orWhere('no_telepon', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('email', 'like', "%{$search}%");
                    });
            });
        }

        $pembimbing = $query
            ->orderBy('nama_lengkap')
            ->get();

        return view('dashboard.admin.pembimbing-data.index', compact('pembimbing'));
    }

    public function create()
    {
        $users = User::where('role', 'pembimbing_lapangan')
            ->whereDoesntHave('pembimbingLapangan')
            ->get();

        return view('dashboard.admin.pembimbing-data.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'nama_lengkap' => 'required|string|max:100',
            'jabatan' => 'nullable|string|max:50',
            'no_telepon' => 'nullable|string|max:15',
            'email_perusahaan' => 'nullable|email|max:100',
            'foto_profil' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto_profil')) {
            $validated['foto_profil'] = $request->file('foto_profil')
                ->store('pembimbing-profil', 'public');
        }

        PembimbingLapangan::updateOrCreate(
            ['user_id' => $validated['user_id']],
            $validated
        );

        return redirect()->route('admin.pembimbing.index')
            ->with('success', 'Data pembimbing lapangan berhasil disimpan');
    }

    public function edit(PembimbingLapangan $pembimbing)
    {
        return view('dashboard.admin.pembimbing-data.edit', compact('pembimbing'));
    }

    public function update(Request $request, PembimbingLapangan $pembimbing)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'jabatan' => 'nullable|string|max:50',
            'no_telepon' => 'nullable|string|max:15',
            'email_perusahaan' => 'nullable|email|max:100',
            'foto_profil' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto_profil')) {
            if ($pembimbing->foto_profil) {
                Storage::disk('public')->delete($pembimbing->foto_profil);
            }

            $validated['foto_profil'] = $request->file('foto_profil')
                ->store('pembimbing-profil', 'public');
        }

        $pembimbing->update($validated);

        return redirect()->route('admin.pembimbing.index')
            ->with('success', 'Data pembimbing lapangan berhasil diperbarui');
    }

    public function destroy(PembimbingLapangan $pembimbing)
    {
        if ($pembimbing->foto_profil) {
            Storage::disk('public')->delete($pembimbing->foto_profil);
        }

        $pembimbing->delete();

        return redirect()->route('admin.pembimbing.index')
            ->with('success', 'Data pembimbing lapangan berhasil dihapus');
    }
}
