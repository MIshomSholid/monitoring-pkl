<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Cloudinary\Cloudinary;

class AdminDataController extends Controller
{
    // LIST DATA ADMIN
    public function index(Request $request)
    {
        $query = Admin::with('user');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('no_telepon', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($qu) use ($search) {
                        $qu->where('email', 'like', "%{$search}%");
                    });
            });
        }

        $admins = $query->latest()->get();

        return view('dashboard.admin.admin-data.index', compact('admins'));
    }

    // FORM CREATE
    public function create()
    {
        $users = User::where('role', 'admin')
            ->whereDoesntHave('admin')
            ->get();

        return view('dashboard.admin.admin-data.create', compact('users'));
    }

    //SIMPAN DATA
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'nama_lengkap' => 'required|string|max:255',
            'no_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'foto_profil' => 'nullable|image|max:2048',
        ]);

        try {
            DB::transaction(function () use ($request) {

                // Inisialisasi path foto
                $fotoPath = null;

                // Memeriksa apakah pengguna mengunggah foto profil
                if ($request->hasFile('foto_profil')) {

                    $cloudinary = new Cloudinary([
                        'cloud' => [
                            'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                            'api_key' => env('CLOUDINARY_API_KEY'),
                            'api_secret' => env('CLOUDINARY_API_SECRET'),
                        ],
                    ]);

                    // Mengunggah foto profil ke Cloudinary
                    $upload = $cloudinary->uploadApi()->upload(
                        $request->file('foto_profil')->getRealPath(),
                        [
                            'folder' => 'admin-profil'
                        ]
                    );

                    $fotoPath = $upload['secure_url']; 
                }

                Admin::create([
                    'user_id' => $request->user_id,
                    'nama_lengkap' => $request->nama_lengkap,
                    'no_telepon' => $request->no_telepon,
                    'alamat' => $request->alamat,
                    'foto_profil' => $fotoPath,
                ]);
            });

        } catch (\Throwable $e) {
            return back()->withErrors('Gagal menambahkan admin')->withInput();
        }

        return redirect()
            ->route('admin.admin-data.index')
            ->with('success', 'Admin berhasil ditambahkan');
    }

    // DETAIL
    public function show($id)
    {
        $admin = Admin::with('user')->findOrFail($id);

        return view('dashboard.admin.admin-data.show', compact('admin'));
    }

    // FORM EDIT
    public function edit($id)
    {
        $admin = Admin::findOrFail($id);

        $users = User::where('role', 'admin')
            ->where(function ($q) use ($admin) {
                $q->whereDoesntHave('admin')
                    ->orWhere('id', $admin->user_id);
            })
            ->get();

        return view('dashboard.admin.admin-data.edit', compact('admin', 'users'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'foto_profil' => 'nullable|image|max:2048',
        ]);

        try {
            DB::transaction(function () use ($request, $admin) {

                // Memeriksa apakah terdapat foto profil baru
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
                            'folder' => 'admin-profil'
                        ]
                    );

                    $admin->foto_profil = $upload['secure_url'];
                }

                $admin->update([
                    'nama_lengkap' => $request->nama_lengkap,
                    'no_telepon' => $request->no_telepon,
                    'alamat' => $request->alamat,
                    'foto_profil' => $admin->foto_profil,
                ]);
            });

        } catch (\Throwable $e) {
            return back()->withErrors('Gagal update admin')->withInput();
        }

        return redirect()
            ->route('admin.admin-data.index')
            ->with('success', 'Admin berhasil diperbarui');
    }

    // DELETE
    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);

        $admin->delete();

        return back()->with('success', 'Admin berhasil dihapus');
    }
}