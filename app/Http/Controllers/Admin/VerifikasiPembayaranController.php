<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Periksa;
use App\Notifications\PembayaranDiverifikasiNotification;
use Illuminate\Http\Request;

class VerifikasiPembayaranController extends Controller
{
    public function index()
    {
        $pembayarans = Periksa::with([
            'daftarPoli.pasien',
            'daftarPoli.jadwalPeriksa.dokter.poli'
        ])
        ->orderByRaw("CASE WHEN status_pembayaran = 'menunggu_verifikasi' THEN 1 WHEN status_pembayaran = 'belum_bayar' THEN 2 ELSE 3 END")
        ->latest('tgl_periksa')
        ->get();

        return view('admin.pembayaran.index', compact('pembayarans'));
    }

    public function konfirmasi($id)
    {
        $periksa = Periksa::with('daftarPoli.pasien')->findOrFail($id);

        $periksa->update([
            'status_pembayaran' => 'lunas',
            'tgl_verifikasi' => now(),
        ]);

        // Kirim Notifikasi ke Pasien bahwa pembayaran telah lunas
        $pasien = $periksa->daftarPoli?->pasien;
        if ($pasien) {
            $pasien->notify(new PembayaranDiverifikasiNotification($periksa));
        }

        return redirect()->route('admin.pembayaran.index')
            ->with('success', 'Pembayaran berhasil diverifikasi. Status tagihan kini lunas.');
    }
}
