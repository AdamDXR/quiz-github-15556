<?php

namespace Database\Seeders;

use App\Models\DaftarPoli;
use App\Models\DetailPeriksa;
use App\Models\JadwalPeriksa;
use App\Models\Obat;
use App\Models\Periksa;
use App\Models\Poli;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. DATA MASTER POLIKLINIK
        $polis = [
            ['id' => 1, 'nama_poli' => 'Poli Umum', 'keterangan' => 'Pelayanan kesehatan umum dan diagnosis pertama'],
            ['id' => 2, 'nama_poli' => 'Poli Gigi', 'keterangan' => 'Pelayanan kesehatan gigi dan mulut'],
            ['id' => 3, 'nama_poli' => 'Poli Anak', 'keterangan' => 'Pelayanan kesehatan bayi, balita, dan anak'],
            ['id' => 4, 'nama_poli' => 'Poli Mata', 'keterangan' => 'Pemeriksaan kesehatan mata dan penglihatan'],
        ];

        foreach ($polis as $p) {
            Poli::updateOrCreate(['id' => $p['id']], $p);
        }

        // 2. DATA USER RESMI BAWAAN PENGUJI
        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'nama' => 'Admin Sistem',
                'password' => Hash::make('admin'),
                'role' => 'admin',
                'no_hp' => '081200000001',
                'alamat' => 'Kantor Administrasi Poliklinik',
            ]
        );

        $dokter = User::updateOrCreate(
            ['email' => 'dokter@gmail.com'],
            [
                'nama' => 'dr. Muhammad Sumbul, Sp.PD',
                'password' => Hash::make('dokter'),
                'role' => 'dokter',
                'id_poli' => 1,
                'no_ktp' => '3301011203850001',
                'no_hp' => '081234567890',
                'alamat' => 'Jl. Pemuda No. 45, Semarang',
            ]
        );

        $pasien = User::updateOrCreate(
            ['email' => 'pasien@gmail.com'],
            [
                'nama' => 'Bambang Wijaya',
                'password' => Hash::make('pasien'),
                'role' => 'pasien',
                'no_rm' => '202610-003',
                'no_ktp' => '3301012508980002',
                'no_hp' => '085712345678',
                'alamat' => 'Jl. Melati No. 12, Semarang',
            ]
        );

        // 3. DATA JADWAL PERIKSA DOKTER
        $jadwal1 = JadwalPeriksa::updateOrCreate(
            ['id_dokter' => $dokter->id, 'hari' => 'Senin'],
            [
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '12:00:00',
            ]
        );

        $jadwal2 = JadwalPeriksa::updateOrCreate(
            ['id_dokter' => $dokter->id, 'hari' => 'Rabu'],
            [
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '12:00:00',
            ]
        );

        $jadwal3 = JadwalPeriksa::updateOrCreate(
            ['id_dokter' => $dokter->id, 'hari' => 'Jumat'],
            [
                'jam_mulai' => '13:00:00',
                'jam_selesai' => '17:00:00',
            ]
        );

        // 4. DATA MASTER OBAT (Lengkap dengan Stok dan Harga sesuai kriteria v2)
        $obats = [
            ['nama_obat' => 'Paracetamol 500mg', 'kemasan' => 'Strip (10 Tab)', 'harga' => 8000, 'stok' => 120],
            ['nama_obat' => 'Amoxicillin 500mg', 'kemasan' => 'Strip (10 Kap)', 'harga' => 12000, 'stok' => 85],
            ['nama_obat' => 'Antasida Doen', 'kemasan' => 'Botol (60 ml)', 'harga' => 15000, 'stok' => 50],
            ['nama_obat' => 'Cetirizine 10mg', 'kemasan' => 'Strip (10 Tab)', 'harga' => 10000, 'stok' => 95],
            ['nama_obat' => 'Vitamin C 500mg', 'kemasan' => 'Botol (30 Tab)', 'harga' => 25000, 'stok' => 60],
            ['nama_obat' => 'Ibuprofen 400mg', 'kemasan' => 'Strip (10 Tab)', 'harga' => 14000, 'stok' => 40],
            ['nama_obat' => 'Dexamethasone 0.5mg', 'kemasan' => 'Strip (10 Tab)', 'harga' => 6000, 'stok' => 110],
            ['nama_obat' => 'Oralit Sachet', 'kemasan' => 'Sachet', 'harga' => 4000, 'stok' => 150],
        ];

        foreach ($obats as $o) {
            Obat::updateOrCreate(['nama_obat' => $o['nama_obat']], $o);
        }

        // 5. SAMPLE RIWAYAT PEMERIKSAAN (Untuk demonstrasi Detail Resep & Biaya)
        $daftarSelesai = DaftarPoli::firstOrCreate(
            [
                'id_pasien' => $pasien->id,
                'id_jadwal' => $jadwal1->id,
                'no_antrian' => 1,
            ],
            [
                'keluhan' => 'Demam tinggi disertai sakit kepala sejak 2 hari yang lalu',
                'status' => 'selesai',
            ]
        );

        if (!$daftarSelesai->periksas()->exists()) {
            $obat1 = Obat::where('nama_obat', 'Paracetamol 500mg')->first();
            $obat2 = Obat::where('nama_obat', 'Vitamin C 500mg')->first();
            $biayaObat = ($obat1->harga ?? 8000) + ($obat2->harga ?? 25000);

            $periksaSample = Periksa::create([
                'id_daftar_poli' => $daftarSelesai->id,
                'tgl_periksa' => now()->subDay(),
                'catatan' => 'Gejala infeksi virus ringan. Istirahat yang cukup, perbanyak minum air putih hangat, dan konsumsi obat teratur setelah makan.',
                'biaya_periksa' => 150000 + $biayaObat,
                'status_pembayaran' => 'belum_bayar',
            ]);

            if ($obat1) {
                DetailPeriksa::create([
                    'id_periksa' => $periksaSample->id,
                    'id_obat' => $obat1->id,
                ]);
            }

            if ($obat2) {
                DetailPeriksa::create([
                    'id_periksa' => $periksaSample->id,
                    'id_obat' => $obat2->id,
                ]);
            }
        }
    }
}
