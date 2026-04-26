<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuruPembimbing;
use App\Models\User;
use Illuminate\Http\Request;
use Cloudinary\Cloudinary;

class GuruPembimbingController extends Controller
{
    /**
     * Tampilkan daftar guru pembimbing
     */
    public function index(Request $request)
    {
        $query = GuruPembimbing::with('user');

        // SEARCH
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('email', 'like', "%{$search}%");
                    });
            });
        }

        $guru = $query
            ->orderBy('nama_lengkap')
            ->get();

        return view('dashboard.admin.guru-data.index', compact('guru'));
    }

    /**
     * Form tambah data diri guru pembimbing
     */
    public function create()
    {
        $users = User::where('role', 'guru_pembimbing')
            ->whereDoesntHave('guruPembimbing')
            ->get();

        return view('dashboard.admin.guru-data.create', compact('users'));
    }

    /**
     * Simpan data diri guru pembimbing
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'nip' => 'required|string|max:20',
            'nama_lengkap' => 'required|string|max:100',
            'mata_pelajaran' => 'nullable|string|max:50',
            'no_telepon' => 'nullable|string|max:15',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Ambil data guru jika sudah ada
        $guru = GuruPembimbing::where('user_id', $validated['user_id'])->first();

        // Upload foto jika ada
        if ($request->hasFile('foto_profil')) {

            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                    'api_key' => env('CLOUDINARY_API_KEY'),
                    'api_secret' => env('CLOUDINARY_API_SECRET'),
                ],
            ]);

            $upload = $cloudinary->uploadApi()->upload(
                $request->file('foto_profil')->getRealPath(),
                [
                    'folder' => 'guru-profil'
                ]
            );

            $validated['foto_profil'] = $upload['secure_url'];
        }

        // CREATE atau UPDATE berdasarkan user_id
        GuruPembimbing::updateOrCreate(
            ['user_id' => $validated['user_id']], // key unik
            $validated
        );

        return redirect()
            ->route('admin.guru.index')
            ->with('success', 'Data guru pembimbing berhasil disimpan');
    }

    /**
     * Form edit data diri guru pembimbing
     */
    public function edit(GuruPembimbing $guru)
    {
        return view('dashboard.admin.guru-data.edit', compact('guru'));
    }

    /**
     * Update data diri guru pembimbing
     */
    public function update(Request $request, GuruPembimbing $guru)
    {
        $validated = $request->validate([
            'nip' => 'required|string|max:20|unique:guru_pembimbing,nip,' . $guru->id,
            'nama_lengkap' => 'required|string|max:100',
            'mata_pelajaran' => 'nullable|string|max:50',
            'no_telepon' => 'nullable|string|max:15',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Ganti foto jika upload baru
        if ($request->hasFile('foto_profil')) {
            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                    'api_key' => env('CLOUDINARY_API_KEY'),
                    'api_secret' => env('CLOUDINARY_API_SECRET'),
                ],
            ]);

            $upload = $cloudinary->uploadApi()->upload(
                $request->file('foto_profil')->getRealPath(),
                [
                    'folder' => 'guru-profil'
                ]
            );

            $validated['foto_profil'] = $upload['secure_url'];
        }

        $guru->update($validated);

        return redirect()
            ->route('admin.guru.index')
            ->with('success', 'Data guru pembimbing berhasil diperbarui');
    }

    /**
     * Hapus data diri guru pembimbing
     */
    public function destroy(GuruPembimbing $guru)
    {
        $guru->delete();

        return redirect()
            ->route('admin.guru.index')
            ->with('success', 'Data guru pembimbing berhasil dihapus');
    }
}
