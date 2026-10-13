<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use Illuminate\Http\Request;

class ObatController extends Controller
{
    /**
     * Menampilkan daftar data obat.
     */
    public function index()
    {
        $obats = Obat::all();
        return view('admin.obat.index', compact('obats'));
    }

    /**
     * Menampilkan form tambah obat.
     */
    public function create()
    {
        return view('admin.obat.create');
    }

    /**
     * Menyimpan data obat baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_obat' => 'required|string|max:255',
            'kemasan'   => 'required|string|max:35',
            'harga'     => 'required|numeric|min:0',
        ]);

        Obat::create($validated);

        return redirect()
            ->route('obat.index')
            ->with('success', 'Data obat berhasil ditambahkan');
    }

    /**
     * Menampilkan form edit obat.
     */
    public function edit(Obat $obat)
    {
        return view('admin.obat.edit', compact('obat'));
    }

    /**
     * Memperbarui data obat di database.
     */
    public function update(Request $request, Obat $obat)
    {
        $validated = $request->validate([
            'nama_obat' => 'required|string|max:255',
            'kemasan'   => 'required|string|max:35',
            'harga'     => 'required|numeric|min:0',
        ]);

        $updateData = [
            'nama_obat' => $request->nama_obat,
            'kemasan' => $request->kemasan,
            'harga' => $request->harga,
        ];

        $obat->update($updateData);

        return redirect()
            ->route('obat.index')
            ->with('success', 'Data obat berhasil diperbarui');
    }

    /**
     * Menghapus data obat dari database.
     */
    public function destroy(Obat $obat)
    {
        $obat->delete();

        return redirect()
            ->route('obat.index')
            ->with('success', 'Data obat berhasil dihapus');
    }
}
