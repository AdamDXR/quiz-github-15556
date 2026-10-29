<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $pasienId = Auth::id();

        // 1. Ambil antrian aktif milik pasien login (status: menunggu atau sedang_dilayani)
        $antrianAktif = DaftarPoli::where('id_pasien', $pasienId)
            ->whereIn('status', ['menunggu', 'sedang_dilayani'])
            ->with(['jadwalPeriksa.dokter.poli'])
            ->latest()
            ->first();

        // 2. Cari nomor yang sedang dilayani pada jadwal antrian aktif pasien
        $nomorSedangDilayani = '-';
        if ($antrianAktif) {
            $sedang = DaftarPoli::where('id_jadwal', $antrianAktif->id_jadwal)
                ->where('status', 'sedang_dilayani')
                ->first();
            $nomorSedangDilayani = $sedang ? $sedang->no_antrian : '-';
        }

        // 3. Ambil seluruh jadwal poli & dokter dan tentukan nomor yang sedang dilayani per jadwal
        $jadwals = JadwalPeriksa::with(['dokter.poli', 'daftarPolis'])->get()->map(function ($jadwal) {
            $sedang = $jadwal->daftarPolis->where('status', 'sedang_dilayani')->first();
            $jadwal->nomor_dilayani = $sedang ? $sedang->no_antrian : '-';
            $jadwal->total_antrian = $jadwal->daftarPolis->whereIn('status', ['menunggu', 'sedang_dilayani'])->count();
            return $jadwal;
        });

        return view('pasien.dashboard', compact('antrianAktif', 'nomorSedangDilayani', 'jadwals'));
    }

    public function statusAntrianJson()
    {
        $pasienId = Auth::id();

        $antrianAktif = DaftarPoli::where('id_pasien', $pasienId)
            ->whereIn('status', ['menunggu', 'sedang_dilayani'])
            ->with(['jadwalPeriksa.dokter.poli'])
            ->first();

        $nomorSedangDilayani = '-';
        if ($antrianAktif) {
            $sedang = DaftarPoli::where('id_jadwal', $antrianAktif->id_jadwal)
                ->where('status', 'sedang_dilayani')
                ->first();
            $nomorSedangDilayani = $sedang ? $sedang->no_antrian : '-';
        }

        $jadwalStatus = JadwalPeriksa::with(['daftarPolis'])->get()->mapWithKeys(function ($j) {
            $sedang = $j->daftarPolis->where('status', 'sedang_dilayani')->first();
            return [$j->id => $sedang ? $sedang->no_antrian : '-'];
        });

        return response()->json([
            'antrian_aktif' => $antrianAktif,
            'nomor_sedang_dilayani' => $nomorSedangDilayani,
            'jadwal_status' => $jadwalStatus,
        ]);
    }
}
