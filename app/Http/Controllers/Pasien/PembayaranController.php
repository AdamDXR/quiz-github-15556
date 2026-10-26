<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\Periksa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class PembayaranController extends Controller
{
    public function index()
    {
        $pembayarans = Periksa::whereHas('daftarPoli', function ($q) {
            $q->where('id_pasien', Auth::id());
        })
        ->with([
            'daftarPoli.jadwalPeriksa.dokter.poli',
            'detailPeriksas.obat'
        ])
        ->latest('tgl_periksa')
        ->get();

        return view('pasien.pembayaran.index', compact('pembayarans'));
    }

    public function upload(Request $request, $id)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'bukti_pembayaran.required' => 'Foto bukti pembayaran wajib diunggah.',
            'bukti_pembayaran.image' => 'File harus berupa gambar.',
            'bukti_pembayaran.mimes' => 'Format gambar harus JPEG, PNG, atau JPG.',
            'bukti_pembayaran.max' => 'Ukuran file gambar maksimal 2MB.',
        ]);

        $periksa = Periksa::whereHas('daftarPoli', function ($q) {
            $q->where('id_pasien', Auth::id());
        })->findOrFail($id);

        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $fileName = 'bukti_' . $periksa->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            $destinationPath = public_path('uploads/bukti_pembayaran');
            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }

            // Hapus file lama jika ada
            if ($periksa->bukti_pembayaran && File::exists(public_path('uploads/bukti_pembayaran/' . $periksa->bukti_pembayaran))) {
                File::delete(public_path('uploads/bukti_pembayaran/' . $periksa->bukti_pembayaran));
            }

            $file->move($destinationPath, $fileName);

            $periksa->update([
                'bukti_pembayaran' => $fileName,
                'status_pembayaran' => 'menunggu_verifikasi',
                'tgl_pembayaran' => now(),
            ]);

            // Di Tahap 5 nanti, notifikasi ke dokter akan dipanggil di sini
        }

        return redirect()->route('pasien.pembayaran.index')
            ->with('success', 'Bukti pembayaran berhasil diunggah dan sedang menunggu verifikasi Admin.');
    }
}
