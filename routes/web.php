<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuditorDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\SinkronisasiController;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Authenticated routes
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Admin LPH Dashboard & Fitur Admin
Route::middleware(['auth', 'role:Admin LPH'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/sinkronisasi', [SinkronisasiController::class, 'index'])->name('admin.sinkronisasi');
    Route::post('/admin/sinkronisasi/simulasi', [SinkronisasiController::class, 'syncSimulation'])->name('admin.sinkronisasi.simulasi');
    
    // Fitur Data Pengajuan
    Route::get('/admin/pengajuan', [PengajuanController::class, 'index'])->name('admin.pengajuan');
    Route::get('/admin/pengajuan/tambah', [PengajuanController::class, 'create'])->name('admin.pengajuan.create');
    Route::post('/admin/pengajuan', [PengajuanController::class, 'store'])->name('admin.pengajuan.store');
    Route::get('/admin/pengajuan/{id}/berhasil', [PengajuanController::class, 'success'])->name('admin.pengajuan.success');
    Route::get('/admin/pengajuan/{id}/edit', [PengajuanController::class, 'edit'])->name('admin.pengajuan.edit');
    Route::put('/admin/pengajuan/{id}', [PengajuanController::class, 'update'])->name('admin.pengajuan.update');
    Route::get('/admin/pengajuan/{id}', [PengajuanController::class, 'show'])->name('admin.pengajuan.show');
});

// Auditor Dashboard
Route::middleware(['auth', 'role:Auditor'])->group(function () {
    Route::get('/auditor/dashboard', [AuditorDashboardController::class, 'index'])->name('auditor.dashboard');
});
