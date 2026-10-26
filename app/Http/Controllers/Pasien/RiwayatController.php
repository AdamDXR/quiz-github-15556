<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    public function index()
    {
        $riwayats = DaftarPoli::with([
            'jadwalPeriksa.dokter.poli',
            'periksas.detailPeriksas.obat'
        ])
        ->where('id_pasien', Auth::id())
        ->latest()
        ->get();

        return view('pasien.riwayat.index', compact('riwayats'));
    }

    public function show($id)
    {
        $riwayat = DaftarPoli::with([
            'jadwalPeriksa.dokter.poli',
            'periksas.detailPeriksas.obat'
        ])
        ->where('id_pasien', Auth::id())
        ->findOrFail($id);

        $periksa = $riwayat->periksas->first();

        if (!$periksa) {
            return redirect()->route('pasien.riwayat.index')
                ->with('error', 'Pemeriksaan untuk pendaftaran ini belum dilakukan.');
        }

        return view('pasien.riwayat.show', compact('riwayat', 'periksa'));
    }
}
