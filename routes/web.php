<?php


use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
// Admin
use App\Http\Controllers\Admin\AdminPoliController;
use App\Http\Controllers\Admin\DokterController;
use App\Http\Controllers\Admin\PasienController;
use App\Http\Controllers\Admin\ObatController;
use App\Http\Controllers\Admin\VerifikasiPembayaranController;

// Dokter
use App\Http\Controllers\Dokter\JadwalPeriksaController;
use App\Http\Controllers\Dokter\PeriksaPasienController;
use App\Http\Controllers\Dokter\RiwayatPasienController;

// Pasien
use App\Http\Controllers\Pasien\DashboardController;
use App\Http\Controllers\Pasien\PoliController;
use App\Http\Controllers\Pasien\RiwayatController;
use App\Http\Controllers\Pasien\PembayaranController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ExportController;


Route::get('/', function () {
    return view('welcome');
});


Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::resource('polis', AdminPoliController::class);
    Route::resource('dokter', DokterController::class);
    Route::resource('pasien', PasienController::class);
    Route::resource('obat', ObatController::class);

    Route::get('/pembayaran', [VerifikasiPembayaranController::class, 'index'])->name('admin.pembayaran.index');
    Route::post('/pembayaran/{id}/konfirmasi', [VerifikasiPembayaranController::class, 'konfirmasi'])->name('admin.pembayaran.konfirmasi');

    Route::get('/export/dokter', [ExportController::class, 'exportDokter'])->name('admin.export.dokter');
    Route::get('/export/pasien', [ExportController::class, 'exportPasien'])->name('admin.export.pasien');
    Route::get('/export/obat', [ExportController::class, 'exportObat'])->name('admin.export.obat');
});


Route::middleware(['auth', 'role:dokter'])->prefix('dokter')->group(function () {
    Route::get('/dashboard', function () {
        return view('dokter.dashboard');
    })->name('dokter.dashboard');

    Route::resource('jadwal-periksa', JadwalPeriksaController::class);

    Route::get('/periksa-pasien', [PeriksaPasienController::class, 'index'])->name('periksa-pasien.index');
    Route::post('/periksa-pasien', [PeriksaPasienController::class, 'store'])->name('periksa-pasien.store');
    Route::get('/periksa-pasien/{id}', [PeriksaPasienController::class, 'create'])->name('periksa-pasien.create');
    Route::post('/periksa-pasien/{id}/panggil', [PeriksaPasienController::class, 'panggil'])->name('periksa-pasien.panggil');

    Route::get('/riwayat-pasien', [RiwayatPasienController::class, 'index'])->name('riwayat-pasien.index');
    Route::get('/riwayat-pasien/{id}', [RiwayatPasienController::class, 'show'])->name('riwayat-pasien.show');

    Route::get('/export/jadwal', [ExportController::class, 'exportJadwalDokter'])->name('dokter.export.jadwal');
    Route::get('/export/riwayat', [ExportController::class, 'exportRiwayatPasienDokter'])->name('dokter.export.riwayat');
});


Route::middleware(['auth', 'role:pasien'])->prefix('pasien')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('pasien.dashboard');
    Route::get('/antrian/status-live', [DashboardController::class, 'statusAntrianJson'])->name('pasien.antrian.live');

    Route::get('/daftar', [PoliController::class, 'get'])->name('pasien.daftar');

    Route::post('/daftar', [PoliController::class, 'submit'])
        ->name('pasien.daftar.submit');

    Route::get('/riwayat-poli', [RiwayatController::class, 'index'])->name('pasien.riwayat.index');
    Route::get('/riwayat-poli/{id}', [RiwayatController::class, 'show'])->name('pasien.riwayat.show');

    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pasien.pembayaran.index');
    Route::post('/pembayaran/{id}/upload', [PembayaranController::class, 'upload'])->name('pasien.pembayaran.upload');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

