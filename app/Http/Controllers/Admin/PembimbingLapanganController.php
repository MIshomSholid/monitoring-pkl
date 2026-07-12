<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PembimbingLapangan;
use App\Models\User;
use Illuminate\Http\Request;
use Cloudinary\Cloudinary;

class PembimbingLapanganController extends Controller
{
    // Menampilkan daftar pembimbing lapangan beserta fitur pencarian
    public function index(Request $request)
    {
        $query = PembimbingLapangan::with('user');

        // SEARCH
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

    // Menampilkan form tambah pembimbing lapangan
    public function create()
    {
        $users = User::where('role', 'pembimbing_lapangan')
            ->whereDoesntHave('pembimbingLapangan')
            ->get();

        return view('dashboard.admin.pembimbing-data.create', compact('users'));
    }

    // Menyimpan data pembimbing lapangan baru
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
                    'folder' => 'pembimbing-profil'
                ]
            );

            $validated['foto_profil'] = $upload['secure_url'];
        }

        PembimbingLapangan::updateOrCreate(
            ['user_id' => $validated['user_id']],
            $validated
        );

        return redirect()->route('admin.pembimbing.index')
            ->with('success', 'Data pembimbing lapangan berhasil disimpan');
    }

    // Menampilkan form edit data pembimbing lapangan
    public function edit(PembimbingLapangan $pembimbing)
    {
        return view('dashboard.admin.pembimbing-data.edit', compact('pembimbing'));
    }

    // Memperbarui data pembimbing lapangan
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
                    'folder' => 'pembimbing-profil'
                ]
            );

            $validated['foto_profil'] = $upload['secure_url'];
        }

        $pembimbing->update($validated);

        return redirect()->route('admin.pembimbing.index')
            ->with('success', 'Data pembimbing lapangan berhasil diperbarui');
    }

    // Menghapus data pembimbing lapangan
    public function destroy(PembimbingLapangan $pembimbing)
    {
        $pembimbing->delete();

        return redirect()->route('admin.pembimbing.index')
            ->with('success', 'Data pembimbing lapangan berhasil dihapus');
    }
}
