<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Periksa;
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

        // Di Tahap 5 nanti, notifikasi ke pasien akan dipanggil di sini

        return redirect()->route('admin.pembayaran.index')
            ->with('success', 'Pembayaran berhasil diverifikasi. Status tagihan kini lunas.');
    }
}
