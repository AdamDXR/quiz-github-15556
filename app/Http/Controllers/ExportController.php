<?php

namespace App\Http\Controllers;

use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use App\Models\Obat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExportController extends Controller
{
    private function downloadExcel($filename, $headers, $rows)
    {
        $callback = function () use ($headers, $rows) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM agar karakter khusus terbaca sempurna dan otomatis di Microsoft Excel
            fputs($file, "\xEF\xBB\xBF");
            
            // Header baris pemisah separator Excel
            fputs($file, "sep=;\n");
            
            // Header kolom
            fputcsv($file, $headers, ';');
            
            // Data baris
            foreach ($rows as $row) {
                fputcsv($file, $row, ';');
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '_' . date('Ymd_His') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    public function exportDokter()
    {
        $dokters = User::where('role', 'dokter')->with('poli')->get();

        $headers = ['No', 'Nama Dokter', 'Poli / Spesialisasi', 'Email', 'No. KTP', 'No. HP', 'Alamat'];
        $rows = [];

        foreach ($dokters as $index => $d) {
            $rows[] = [
                $index + 1,
                $d->nama ?? $d->name,
                $d->poli->nama_poli ?? '-',
                $d->email,
                "'" . ($d->no_ktp ?? '-'),
                "'" . ($d->no_hp ?? '-'),
                $d->alamat ?? '-',
            ];
        }

        return $this->downloadExcel('Data_Dokter', $headers, $rows);
    }

    public function exportPasien()
    {
        $pasiens = User::where('role', 'pasien')->get();

        $headers = ['No', 'Nama Pasien', 'No. Rekam Medis (RM)', 'Email', 'No. KTP', 'No. HP', 'Alamat'];
        $rows = [];

        foreach ($pasiens as $index => $p) {
            $rows[] = [
                $index + 1,
                $p->nama ?? $p->name,
                $p->no_rm ?? '-',
                $p->email,
                "'" . ($p->no_ktp ?? '-'),
                "'" . ($p->no_hp ?? '-'),
                $p->alamat ?? '-',
            ];
        }

        return $this->downloadExcel('Data_Pasien', $headers, $rows);
    }

    public function exportObat()
    {
        $obats = Obat::all();

        $headers = ['No', 'Nama Obat', 'Kemasan', 'Harga (Rp)', 'Stok'];
        $rows = [];

        foreach ($obats as $index => $o) {
            $rows[] = [
                $index + 1,
                $o->nama_obat,
                $o->kemasan ?? '-',
                $o->harga,
                $o->stok ?? 0,
            ];
        }

        return $this->downloadExcel('Data_Obat', $headers, $rows);
    }

    public function exportJadwalDokter()
    {
        $jadwals = JadwalPeriksa::where('id_dokter', Auth::id())->get();

        $headers = ['No', 'Hari', 'Jam Mulai', 'Jam Selesai'];
        $rows = [];

        foreach ($jadwals as $index => $j) {
            $rows[] = [
                $index + 1,
                $j->hari,
                substr($j->jam_mulai, 0, 5) . ' WIB',
                substr($j->jam_selesai, 0, 5) . ' WIB',
            ];
        }

        return $this->downloadExcel('Jadwal_Periksa_Dokter', $headers, $rows);
    }

    public function exportRiwayatPasienDokter()
    {
        $riwayats = DaftarPoli::whereHas('jadwalPeriksa', function ($q) {
            $q->where('id_dokter', Auth::id());
        })
        ->whereHas('periksas')
        ->with(['pasien', 'periksas.detailPeriksas.obat'])
        ->get();

        $headers = ['No', 'No. Antrian', 'Nama Pasien', 'No. RM', 'Tanggal Periksa', 'Keluhan', 'Catatan Dokter', 'Obat Diresepkan', 'Total Biaya (Rp)'];
        $rows = [];

        foreach ($riwayats as $index => $r) {
            $periksa = $r->periksas->first();
            $obatList = $periksa ? $periksa->detailPeriksas->map(function ($dp) {
                return $dp->obat->nama_obat ?? '';
            })->filter()->implode(', ') : '-';

            $rows[] = [
                $index + 1,
                $r->no_antrian,
                $r->pasien->nama ?? $r->pasien->name ?? '-',
                $r->pasien->no_rm ?? '-',
                $periksa ? \Carbon\Carbon::parse($periksa->tgl_periksa)->format('d/m/Y H:i') : '-',
                $r->keluhan ?: '-',
                $periksa->catatan ?? '-',
                $obatList ?: '-',
                $periksa->biaya_periksa ?? 0,
            ];
        }

        return $this->downloadExcel('Riwayat_Pasien_Dokter', $headers, $rows);
    }
}
