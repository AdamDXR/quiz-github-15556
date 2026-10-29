<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use App\Models\Poli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PoliController extends Controller
{
    public function get()
    {
        $user = Auth::user();
        $polis = Poli::all();
        $jadwal = JadwalPeriksa::with('dokter', 'dokter.poli')->get();

        return view('pasien.daftar', [
            'user' => $user,
            'polis' => $polis,
            'jadwals' => $jadwal,
        ]);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'id_poli' => 'required|exists:poli,id',
            'id_jadwal' => 'required|exists:jadwal_periksa,id',
            'keluhan' => 'nullable|string',
            'id_pasien' => 'required|exists:users,id',
        ]);

        $pasienId = Auth::id() ?? $request->id_pasien;

        // Constraint PDF: Pasien tidak dapat mendaftar ke dua poli sekaligus.
        // Pasien hanya dapat mendaftar kembali setelah proses pemeriksaan selesai.
        $antrianAktif = DaftarPoli::where('id_pasien', $pasienId)
            ->whereIn('status', ['menunggu', 'sedang_dilayani'])
            ->with('jadwalPeriksa.dokter.poli')
            ->first();

        if ($antrianAktif) {
            $namaPoli = $antrianAktif->jadwalPeriksa->dokter->poli->nama_poli ?? 'Poli';
            return redirect()
                ->back()
                ->with('message', "Anda masih memiliki antrian aktif (No. {$antrianAktif->no_antrian}) di {$namaPoli}. Harap selesaikan proses pemeriksaan terlebih dahulu sebelum mendaftar kembali.")
                ->with('type', 'danger');
        }

        $jumlahSudahDaftar = DaftarPoli::where('id_jadwal', $request->id_jadwal)->count();

        DaftarPoli::create([
            'id_pasien' => Auth::id() ?? $request->id_pasien,
            'id_jadwal' => $request->id_jadwal,
            'keluhan' => $request->keluhan ?? '-',
            'no_antrian' => $jumlahSudahDaftar + 1,
            'status' => 'menunggu',
        ]);

        return redirect()
            ->back()
            ->with('message', 'Berhasil Mendaftar ke Poli')
            ->with('type', 'success');
    }
}