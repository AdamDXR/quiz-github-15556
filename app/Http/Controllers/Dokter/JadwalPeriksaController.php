<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\JadwalPeriksa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalPeriksaController extends Controller
{
    /**
     * Menampilkan daftar jadwal periksa milik dokter yang sedang login.
     */
    public function index()
    {
        $jadwals = JadwalPeriksa::where('id_dokter', Auth::id())
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('jam_mulai')
            ->get();

        return view('dokter.jadwal-periksa.index', compact('jadwals'));
    }

    /**
     * Menampilkan form tambah jadwal periksa.
     */
    public function create()
    {
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        return view('dokter.jadwal-periksa.create', compact('hariList'));
    }

    /**
     * Menyimpan data jadwal baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'hari'        => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
        ], [
            'hari.required'        => 'Hari wajib dipilih.',
            'hari.in'              => 'Pilihan hari tidak valid.',
            'jam_mulai.required'   => 'Jam mulai wajib diisi.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jam_selesai.after'    => 'Jam selesai harus lebih akhir dari jam mulai.',
        ]);

        JadwalPeriksa::create([
            'id_dokter'   => Auth::id(),
            'hari'        => $request->hari,
            'jam_mulai'   => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
        ]);

        return redirect()
            ->route('jadwal-periksa.index')
            ->with('success', 'Jadwal periksa berhasil ditambahkan');
    }

    /**
     * Menampilkan form edit jadwal periksa milik dokter yang login.
     */
    public function edit(string $id)
    {
        $jadwal = JadwalPeriksa::where('id_dokter', Auth::id())->findOrFail($id);
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        return view('dokter.jadwal-periksa.edit', compact('jadwal', 'hariList'));
    }

    /**
     * Memperbarui data jadwal periksa di database.
     */
    public function update(Request $request, string $id)
    {
        $jadwal = JadwalPeriksa::where('id_dokter', Auth::id())->findOrFail($id);

        $request->validate([
            'hari'        => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
        ], [
            'hari.required'        => 'Hari wajib dipilih.',
            'hari.in'              => 'Pilihan hari tidak valid.',
            'jam_mulai.required'   => 'Jam mulai wajib diisi.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jam_selesai.after'    => 'Jam selesai harus lebih akhir dari jam mulai.',
        ]);

        $jadwal->update([
            'hari'        => $request->hari,
            'jam_mulai'   => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
        ]);

        return redirect()
            ->route('jadwal-periksa.index')
            ->with('success', 'Jadwal periksa berhasil diperbarui');
    }

    /**
     * Menghapus data jadwal periksa dari database.
     */
    public function destroy(string $id)
    {
        $jadwal = JadwalPeriksa::where('id_dokter', Auth::id())->findOrFail($id);
        $jadwal->delete();

        return redirect()
            ->route('jadwal-periksa.index')
            ->with('success', 'Jadwal periksa berhasil dihapus');
    }
}
