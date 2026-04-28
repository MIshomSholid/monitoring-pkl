<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminDataController extends Controller
{
    /**
     * ========================
     * LIST DATA ADMIN
     * ========================
     */
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

    /**
     * ========================
     * FORM CREATE
     * ========================
     */
    public function create()
    {
        return view('dashboard.admin.admin-data.create');
    }

    /**
     * ========================
     * SIMPAN DATA
     * ========================
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|min:6',
            'no_telepon'   => 'nullable|string|max:20',
            'alamat'       => 'nullable|string',
            'foto_profil'  => 'nullable|image|max:2048',
        ]);

        try {
            DB::transaction(function () use ($request) {

                // CREATE USER LOGIN
                $user = User::create([
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => 'admin',
                    'is_active' => 1
                ]);

                // HANDLE FOTO
                $fotoPath = null;
                if ($request->hasFile('foto_profil')) {
                    $fotoPath = $request->file('foto_profil')
                        ->store('admin-profil', 'public');
                }

                // CREATE ADMIN
                Admin::create([
                    'user_id'      => $user->id,
                    'nama_lengkap' => $request->nama_lengkap,
                    'no_telepon'   => $request->no_telepon,
                    'alamat'       => $request->alamat,
                    'foto_profil'  => $fotoPath,
                ]);

            });

        } catch (\Throwable $e) {
            return back()->withErrors('Gagal menambahkan admin')->withInput();
        }

        return redirect()
            ->route('admin.admin-data.index')
            ->with('success', 'Admin berhasil ditambahkan');
    }

    /**
     * ========================
     * DETAIL
     * ========================
     */
    public function show($id)
    {
        $admin = Admin::with('user')->findOrFail($id);

        return view('dashboard.admin.admin-data.show', compact('admin'));
    }

    /**
     * ========================
     * FORM EDIT
     * ========================
     */
    public function edit($id)
    {
        $admin = Admin::with('user')->findOrFail($id);

        return view('dashboard.admin.admin-data.edit', compact('admin'));
    }

    /**
     * ========================
     * UPDATE
     * ========================
     */
    public function update(Request $request, $id)
    {
        $admin = Admin::with('user')->findOrFail($id);

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin->user_id,
            'no_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'foto_profil' => 'nullable|image|max:2048',
        ]);

        try {
            DB::transaction(function () use ($request, $admin) {

                // UPDATE USER
                $admin->user->update([
                    'email' => $request->email
                ]);

                // HANDLE FOTO
                if ($request->hasFile('foto_profil')) {
                    $fotoPath = $request->file('foto_profil')
                        ->store('admin-profil', 'public');

                    $admin->foto_profil = $fotoPath;
                }

                // UPDATE ADMIN
                $admin->update([
                    'nama_lengkap' => $request->nama_lengkap,
                    'no_telepon'   => $request->no_telepon,
                    'alamat'       => $request->alamat,
                    'foto_profil'  => $admin->foto_profil,
                ]);

            });

        } catch (\Throwable $e) {
            return back()->withErrors('Gagal update admin')->withInput();
        }

        return redirect()
            ->route('admin.admin-data.index')
            ->with('success', 'Admin berhasil diperbarui');
    }

    /**
     * ========================
     * DELETE
     * ========================
     */
    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);

        DB::transaction(function () use ($admin) {
            $admin->user->delete();
            $admin->delete();
        });

        return back()->with('success', 'Admin berhasil dihapus');
    }
}