<?php

namespace App\Http\Controllers\Dokter;

use App\Events\AntrianUpdated;
use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\DetailPeriksa;
use App\Models\Obat;
use App\Models\Periksa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeriksaPasienController extends Controller
{
    public function index()
    {
        $dokterId = Auth::id();

        $daftarPasien = DaftarPoli::with(['pasien', 'jadwalPeriksa', 'periksas'])
            ->whereHas('jadwalPeriksa', function ($query) use ($dokterId) {
                $query->where('id_dokter', $dokterId);
            })
            ->orderByRaw("CASE WHEN status = 'sedang_dilayani' THEN 1 WHEN status = 'menunggu' THEN 2 ELSE 3 END")
            ->orderBy('no_antrian')
            ->get();

        return view('dokter.periksa-pasien.index', compact('daftarPasien'));
    }

    public function panggil($id)
    {
        $dokterId = Auth::id();
        $daftarPoli = DaftarPoli::whereHas('jadwalPeriksa', function ($query) use ($dokterId) {
            $query->where('id_dokter', $dokterId);
        })->with(['jadwalPeriksa.dokter.poli', 'pasien'])->findOrFail($id);

        // Reset antrian lain yang sebelumnya berstatus sedang_dilayani pada jadwal ini
        DaftarPoli::where('id_jadwal', $daftarPoli->id_jadwal)
            ->where('status', 'sedang_dilayani')
            ->where('id', '!=', $id)
            ->update(['status' => 'menunggu']);

        $daftarPoli->update(['status' => 'sedang_dilayani']);

        // Pancarkan event real-time WebSocket
        try {
            broadcast(new AntrianUpdated($daftarPoli));
        } catch (\Throwable $e) {
            // Reverb fallback jika offline saat presentasi
        }

        return redirect()->route('periksa-pasien.index')
            ->with('message', 'Nomor antrian ' . $daftarPoli->no_antrian . ' (' . ($daftarPoli->pasien->nama ?? 'Pasien') . ') berhasil dipanggil.')
            ->with('type', 'success');
    }

    public function create($id)
    {
        $obats = Obat::all();
        return view('dokter.periksa-pasien.create', compact('obats', 'id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'obat_json' => 'required',
            'catatan' => 'nullable|string',
            'biaya_periksa' => 'required|integer',
        ]);

        $obatIds = json_decode($request->obat_json, true);

        $periksa = Periksa::create([
            'id_daftar_poli' => $request->id_daftar_poli,
            'tgl_periksa' => now(),
            'catatan' => $request->catatan,
            'biaya_periksa' => $request->biaya_periksa + 150000,
        ]);

        if (is_array($obatIds)) {
            foreach ($obatIds as $idObat) {
                DetailPeriksa::create([
                    'id_periksa' => $periksa->id,
                    'id_obat' => $idObat,
                ]);
            }
        }

        // Tandai antrian pendaftaran selesai
        $daftarPoli = DaftarPoli::with(['jadwalPeriksa.dokter.poli', 'pasien'])->find($request->id_daftar_poli);
        if ($daftarPoli) {
            $daftarPoli->update(['status' => 'selesai']);

            try {
                broadcast(new AntrianUpdated($daftarPoli));
            } catch (\Throwable $e) {
                // Reverb fallback
            }
        }

        return redirect()
            ->route('periksa-pasien.index')
            ->with('success', 'Data periksa berhasil disimpan.');
    }
}
