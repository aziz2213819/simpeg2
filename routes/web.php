<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StrukturalController;
use App\Http\Controllers\TamuController;
use App\Http\Controllers\TpsController;
use App\Livewire\PositionLive;
use App\Livewire\RankGradeLive;
use Illuminate\Support\Facades\Route;

// --- AKSES PUBLIK (WARGA) ---

// Route Home (Landing Page) - Sekarang bersih tanpa panggil data Report
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/profil-dlh', [TamuController::class, 'profilDLH'])->name('profil.dlh');
Route::get('/pengaduan', [TamuController::class, 'create'])->name('pengaduan.create'); // trigger service
Route::post('/pengaduan', [TamuController::class, 'store'])->name('pengaduan.store')->middleware('throttle:pengaduan_sampah');
Route::get('/alur-lapor', [TamuController::class, 'alurLapor'])->name('alur-lapor');
Route::get('/cek-status', [TamuController::class, 'cekStatus'])->name('cek.status');
Route::get('/lokasi-tps', [TamuController::class, 'tpsWarga'])->name('tamu.tps');


// Detail Pengaduan dipindah ke LUAR agar warga bisa lihat progress lewat link/ID Tracking
Route::get('/pengaduan/{pengaduan}', [ReportController::class, 'show'])->name('pengaduan.show');


// --- AKSES ADMIN (DASHBOARD UMUM) ---
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard'); // trigger service
});


// --- KHUSUS ADMIN SIMPEG ---
Route::middleware(['auth', 'admin', 'isAdminSimpeg'])->group(function () {
    Route::prefix("admin")->group(function () {
        Route::get('/pangkat', RankGradeLive::class)->name('pangkat.index');
        Route::get('/jabatan', PositionLive::class)->name('jabatan.index');
        Route::resource('pegawai', EmployeeController::class);
        Route::resource('notifikasi', NotificationController::class);
        Route::post('/notifikasi/{notification}/send', [AdminController::class, 'notificationSend'])->name('notifikasi.send');

        Route::resource('struktural', StrukturalController::class)->except(['show', 'edit', 'update']);
        Route::patch('struktural/{struktural}/activate', [StrukturalController::class, 'activate'])->name('struktural.activate');
    });
    Route::get('/pegawai/export', [EmployeeController::class, 'export'])->name('pegawai.export');
    Route::get('/pegawai/pdf-kgb', [EmployeeController::class, 'exportPdfKgb'])->name('pegawai.kgb.pdf');
    Route::get('/pegawai/pdf-pensiun', [EmployeeController::class, 'exportPdfPensiun'])->name('pegawai.pensiun.pdf');
});


// --- KHUSUS ADMIN SAMPAH (DLH CARE) ---
Route::middleware(['auth', 'admin', 'isAdminSampah'])->group(function () {
    Route::prefix("admin")->group(function () {
        Route::get('/pengaduan', [ReportController::class, 'index'])->name('admin.pengaduan.index');
        Route::patch('/pengaduan/{pengaduan}/status', [ReportController::class, 'updateStatus'])->name('admin.pengaduan.status');
        Route::delete('/pengaduan/{pengaduan}', [ReportController::class, 'destroy'])->name('admin.pengaduan.destroy');
        // --- ROUTE TPS ---
        Route::get('/tps', [TpsController::class, 'index'])->name('admin.tps.index');
        Route::post('/tps', [TpsController::class, 'store'])->name('admin.tps.store');
        
        // TAMBAHKAN DUA BARIS INI:
        Route::get('/tps/{tps}/edit', [TpsController::class, 'edit'])->name('admin.tps.edit');
        Route::put('/tps/{tps}', [TpsController::class, 'update'])->name('admin.tps.update');
        
        Route::delete('/tps/{tps}', [TpsController::class, 'destroy'])->name('admin.tps.destroy');
        // Route Simpan Komentar Petugas
        Route::post('/pengaduan/{pengaduan}/comment', [ReportController::class, 'storeComment'])->name('admin.pengaduan.comment');
    });
});

    
// --- AKSES PEGAWAI ---
Route::middleware(['auth', 'isPegawai'])->group(function () {
    Route::get('/homepage', [PegawaiController::class, 'index'])->name('pegawai.homepage');
    Route::get('/profil', [PegawaiController::class, 'profile'])->name('pegawai.profil');
    Route::get('/password', [PegawaiController::class, 'password'])->name('pegawai.password');
    Route::get('/duafaktor', [PegawaiController::class, 'duafaktor'])->name('pegawai.duafaktor')->middleware('password.confirm');
    Route::get('/tampilan', [PegawaiController::class, 'tampilan'])->name('pegawai.tampilan');
    Route::patch('/profil/email', [PegawaiController::class, 'updateEmail'])->name('profile.email.update');
    Route::patch('/profil/password', [PegawaiController::class, 'updatePassword'])->name('profile.password.update');
    Route::get('/notifikasi', [PegawaiController::class, 'notification'])->name('pegawai.notifikasi');
    Route::patch('/notifikasi/{id}/read', [PegawaiController::class, 'read'])->name('pegawai.notifikasi.read');
    Route::get('/notifikasi/{notification}', [PegawaiController::class, 'notificationShow'])->name('pegawai.notifikasi.show');
    Route::post('/notifikasi/{notification}', [PegawaiController::class, 'notificationUpdate'])->name('pegawai.notifikasi.update');

    Route::post('/pegawai/2fa/enable', [PegawaiController::class, 'enable2fa'])->name('pegawai.2fa.enable');
    Route::delete('/pegawai/2fa/disable', [PegawaiController::class, 'disable2fa'])->name('pegawai.2fa.disable');
    Route::post('/2fa/confirm', [PegawaiController::class, 'confirm2fa'])->name('pegawai.2fa.confirm');
});

require __DIR__.'/settings.php';