<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Siswa::with('user');

        // SEARCH
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nis', 'like', '%' . $request->search . '%')
                    ->orWhere('nama_lengkap', 'like', '%' . $request->search . '%');
            });
        }

        $siswas = $query->latest()->paginate(10)->withQueryString();

        return view('dashboard.admin.siswa-data.index', compact('siswas'));
    }

    public function create()
    {
        $users = User::where('role', 'siswa')
            ->whereDoesntHave('siswa')
            ->get();

        return view('dashboard.admin.siswa-data.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|unique:siswa,user_id',
            'nis' => 'required|unique:siswa,nis',
            'nama_lengkap' => 'required|string|max:100',
            'kelas' => 'required|string|max:20',
            'jurusan' => 'required|string|max:50',
            'no_telepon' => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        DB::transaction(function () use ($request) {
            $pathFoto = null;
            if ($request->hasFile('foto_profil')) {
                $pathFoto = $request->file('foto_profil')->store('siswa-profil', 'public');
            }

            Siswa::create([
                'user_id' => $request->user_id,
                'nis' => $request->nis,
                'nama_lengkap' => $request->nama_lengkap,
                'kelas' => $request->kelas,
                'jurusan' => $request->jurusan,
                'no_telepon' => $request->no_telepon,
                'alamat' => $request->alamat,
                'foto_profil' => $pathFoto
            ]);
        });

        return redirect()
            ->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil ditambahkan');
    }

    // ================= DETAIL =================
    public function show(Siswa $siswa)
    {
        $siswa->load('user');

        return view('dashboard.admin.siswa-data.show', compact('siswa'));
    }

    // ================= FORM EDIT =================
    public function edit(Siswa $siswa)
    {
        return view('dashboard.admin.siswa-data.edit', compact('siswa'));
    }

    // ================= UPDATE =================
    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nis' => 'required|unique:siswa,nis,' . $siswa->id,
            'nama_lengkap' => 'required|string|max:100',
            'kelas' => 'required|string|max:20',
            'jurusan' => 'required|string|max:50',
            'no_telepon' => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        DB::transaction(function () use ($request, $siswa) {

            $data = $request->only([
                'nis',
                'nama_lengkap',
                'kelas',
                'jurusan',
                'no_telepon',
                'alamat'
            ]);

            if ($request->hasFile('foto_profil')) {

                // hapus foto lama
                if ($siswa->foto_profil) {
                    Storage::disk('public')->delete($siswa->foto_profil);
                }

                $data['foto_profil'] = $request->file('foto_profil')
                    ->store('siswa-profil', 'public');
            }

            $siswa->update($data);
        });

        return redirect()
            ->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui');
    }

    // ================= DELETE =================
    public function destroy(Siswa $siswa)
    {
        $siswa->delete();

        return redirect()
            ->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil dihapus');
    }

}
