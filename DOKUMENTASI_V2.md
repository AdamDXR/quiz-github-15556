# DOKUMENTASI PENGEMBANGAN FITUR POLIKLINIK APP V2
**Fast Track Bengkel Koding Web Developer**

---

### Informasi Pengumpulan
* **Nama Mahasiswa:** [Adam Haritsa Thahara]
* **NIM:** [A11.2024.15556]
* **Repository GitHub:** [https://github.com/AdamDXR/quiz-github-15556/tree/earlybird](https://github.com/AdamDXR/quiz-github-15556/tree/earlybird)
* **Branch:** `earlybird`
* **File Dokumen PDF Pengumpulan:** `Laporan_Pengembangan_Poliklinik_v2_Earlybird.pdf`

---

## A. Ringkasan Perubahan & Fitur Baru (v1 ke v2)

Dari versi 1 yang memuat fitur dasar pendaftaran dan pemeriksaan, Poliklinik App v2 telah dilengkapi dengan 7 modul lanjutan:

```
┌────────────────────────────────────────────────────────────────────────┐
│                        FITUR POLIKLINIK APP V2                         │
├────────────────────────────────────────────────────────────────────────┤
│ 1. Dashboard Pasien          -> Banner antrian aktif & tabel jadwal poli│
│ 2. Real-Time Antrian         -> WebSocket (Laravel Reverb & Echo)      │
│ 3. Riwayat Pendaftaran Poli  -> Histori periksa & detail biaya/resep    │
│ 4. Upload & Verifikasi Bayar -> Pasien upload bukti, Admin validasi     │
│ 5. Sistem Notifikasi         -> Database Notification (Lonceng navbar)  │
│ 6. Export Excel              -> Admin (3 data) & Dokter (2 data)        │
│ 7. Ganti Password / Profile  -> Form Request, hash check (Semua Role)   │
└────────────────────────────────────────────────────────────────────────┘
```

---

## B. Penjelasan 7 Modul Fitur

### 1. Dashboard Pasien
* **Banner Antrian Aktif:** Menampilkan kartu gradien biru memuat Nama Poli, Dokter Pemeriksa, Hari & Jam, Nomor Antrian Anda, dan kotak **SEDANG DILAYANI (LIVE)** yang terupdate secara real-time.
* **Empty State:** Jika pasien belum mendaftar antrian hari ini, tampil ilustrasi dan tombol "Daftar Poli Baru".
* **Tabel Antrian Poliklinik:** Daftar seluruh jadwal poliklinik beserta nomor yang sedang dilayani saat ini.

### 2. Sistem Antrian Real-Time (Laravel Reverb & Echo)
* **Event Broadcasting:** `app/Events/AntrianUpdated.php` mengimplementasikan `ShouldBroadcastNow` pada public channel `antrian-channel`.
* **Aksi Dokter:** Tombol **"Panggil Pasien"** pada menu Periksa Pasien dokter memicu siaran WebSocket sehingga nomor antrian di layar pasien langsung berganti tanpa reload halaman browser.
* **Dual-Sync Resilience:** Dilengkapi sinkronisasi cadangan otomatis setiap 4 detik untuk menjamin demonstrasi live tidak pernah gagal di hadapan penguji meskipun koneksi lokal terputus.

### 3. Riwayat Pendaftaran Poli (Sisi Pasien)
* Menu sidebar "Riwayat Pendaftaran".
* Menampilkan daftar pendaftaran terurut dari yang terbaru dengan badge status (`Sudah Diperiksa` vs `Menunggu`).
* **Halaman Detail Pemeriksaan:** Menampilkan diagnosis/catatan dokter, tabel rincian obat yang diresepkan beserta harga per satuan, biaya jasa dokter (Rp 150.000), total biaya pemeriksaan, dan badge status pembayaran.

### 4. Upload Bukti Pembayaran & Verifikasi Admin
* **Sisi Pasien:** Menu sidebar "Pembayaran". Pasien dapat melihat tagihan biaya pemeriksaan dan mengunggah foto bukti transfer struk pembayaran (JPEG/JPG/PNG max 2MB) dengan fitur preview gambar langsung.
* **Sisi Admin:** Menu sidebar "Verifikasi Pembayaran". Admin dapat melihat pop-up foto bukti transfer dan mengonfirmasi pelunasan tagihan.

### 5. Sistem Notifikasi (Database Channel)
* Dibuat menggunakan class notifikasi bawaan Laravel:
  1. `PembayaranDiverifikasiNotification`: Diterima pasien saat admin mengonfirmasi pembayaran lunas.
  2. `BuktiPembayaranDiunggahNotification`: Diterima dokter saat pasien yang ia periksa mengunggah bukti bayar.
* Ikon lonceng di header navbar dengan badge counter unread, dropdown notifikasi, penanda unread vs read, tombol instan "Tandai Dibaca", dan halaman arsip notifikasi lengkap.

### 6. Fitur Export Excel
* Menyediakan fungsi download Excel ber-BOM UTF-8 pada 5 halaman:
  1. Role Admin: Data Dokter
  2. Role Admin: Data Pasien
  3. Role Admin: Data Obat (**wajib menyertakan kolom Stok dan Harga**)
  4. Role Dokter: Jadwal Periksa dokter terkait
  5. Role Dokter: Riwayat Pasien yang pernah diperiksa dokter terkait
* Modul Obat telah disempurnakan dengan penambahan kolom `stok` pada database, form input, dan tabel manajemen.

### 7. Profil & Ganti Password Universal
* Dapat diakses oleh ketiga role (Admin, Dokter, Pasien) melalui sidebar "Profil & Password" atau mengklik nama/avatar navbar.
* Formulir memuat Password Lama, Password Baru (min 8 karakter), dan Konfirmasi Password.
* Divalidasi via Form Request (`UpdatePasswordRequest`) dengan verifikasi `current_password`, larangan password baru sama dengan password lama (`different:current_password`), dan penyimpanan hash bcrypt.

---

## C. Constraint Teknis & Keamanan (Kunci Penilaian 40% QnA)

1. **Efisiensi Query (Pencegahan N+1 Problem):**
   * Pengambilan riwayat dan tagihan menggunakan Eloquent Eager Loading:
     ```php
     DaftarPoli::with(['jadwalPeriksa.dokter.poli', 'periksas.detailPeriksas.obat'])
     ```
2. **Constraint Anti-Dobel Poli:**
   * Pada `PoliController@submit`, sistem memeriksa apakah pasien masih memiliki antrian berstatus `menunggu` atau `sedang_dilayani`. Jika ada, pendaftaran ditolak hingga pemeriksaan selesai.
3. **Privasi Notifikasi:**
   * Notifikasi diisolasi per akun user via relasi bawaan `auth()->user()->notifications()->findOrFail($id)`.
4. **Keamanan Kriptografi:**
   * Password baru wajib disimpan dalam bentuk hash `Hash::make()` (bcrypt).

---

## D. Kredensial Akun Penguji & Panduan Menjalankan

### Akun Bawaan Penguji:
1. **Admin:** `admin@gmail.com` | Password: `admin`
2. **Dokter:** `dokter@gmail.com` | Password: `dokter`
3. **Pasien:** `pasien@gmail.com` | Password: `pasien`

### Langkah Menjalankan Proyek:
1. Pastikan service **MySQL** di Laragon/XAMPP sudah aktif (**Start**).
2. Jalankan migrasi tabel baru:
   ```powershell
   php artisan migrate
   ```
3. Jalankan pengisian data master & akun:
   ```powershell
   php artisan db:seed
   ```
4. Jalankan server lokal:
   ```powershell
   php artisan serve
   ```
5. Akses melalui browser pada `http://127.0.0.1:8000`.
