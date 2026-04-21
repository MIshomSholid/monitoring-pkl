<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InformasiPkl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InformasiPklController extends Controller
{
    /**
     * List informasi PKL
     */
    public function index()
    {
        $informasi = InformasiPkl::orderBy('tanggal_publish', 'desc')->get();

        return view('dashboard.admin.informasi-pkl.index', compact('informasi'));
    }

    /**
     * Form create
     */
    public function create()
    {
        return view('dashboard.admin.informasi-pkl.create');
    }

    /**
     * Simpan data
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'           => 'required|string|max:200',
            'konten'          => 'required|string',
            'tipe'            => 'required|in:informasi,jadwal,ketentuan',
            'tanggal_publish' => 'required|date',
            'is_published'    => 'nullable|boolean',
        ]);

        InformasiPkl::create([
            'judul'           => $validated['judul'],
            'konten'          => $validated['konten'],
            'tipe'            => $validated['tipe'],
            'tanggal_publish' => $validated['tanggal_publish'],
            'is_published'    => $request->boolean('is_published'),
            'created_by'      => Auth::id(),
        ]);

        return redirect()
            ->route('admin.informasi-pkl.index')
            ->with('success', 'Informasi PKL berhasil ditambahkan');
    }

    /**
     * Form edit
     */
    public function edit(InformasiPkl $informasiPkl)
    {
        return view(
            'dashboard.admin.informasi-pkl.edit',
            ['informasi' => $informasiPkl]
        );
    }

    /**
     * Update data
     */
    public function update(Request $request, InformasiPkl $informasiPkl)
    {
        $validated = $request->validate([
            'judul'           => 'required|string|max:200',
            'konten'          => 'required|string',
            'tipe'            => 'required|in:informasi,jadwal,ketentuan',
            'tanggal_publish' => 'required|date',
            'is_published'    => 'nullable|boolean',
        ]);

        $informasiPkl->update([
            'judul'           => $validated['judul'],
            'konten'          => $validated['konten'],
            'tipe'            => $validated['tipe'],
            'tanggal_publish' => $validated['tanggal_publish'],
            'is_published'    => $request->boolean('is_published'),
        ]);

        return redirect()
            ->route('admin.informasi-pkl.index')
            ->with('success', 'Informasi PKL berhasil diperbarui');
    }

    /**
     * Hapus data
     */
    public function destroy(InformasiPkl $informasiPkl)
    {
        $informasiPkl->delete();

        return redirect()
            ->route('admin.informasi-pkl.index')
            ->with('success', 'Informasi PKL berhasil dihapus');
    }
}   
