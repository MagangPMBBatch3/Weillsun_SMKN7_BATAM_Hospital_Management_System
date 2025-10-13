<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [AuthController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    
    Route::get('jadwalTenagaMedis', [AuthController::class, 'jadwalTenagaMedis'])->name('jadwalTenagaMedis.index');
    
    Route::get('kunjungan', [AuthController::class, 'kunjungan'])->name('kunjungan.index');
    
    Route::get('labPemeriksaan', [AuthController::class, 'labPemeriksaan'])->name('labPemeriksaan.index');
    
    Route::get('logAktivitas', [AuthController::class, 'logAktivitas'])->name('logAktivitas.index');
    
    Route::get('obat', [AuthController::class, 'obat'])->name('obat.index');
    
    Route::get('pasien', [AuthController::class, 'pasien'])->name('pasien.index');
    
    Route::get('pembayaranPasien', [AuthController::class, 'pembayaranPasien'])->name('pembayaranPasien.index');
    
    Route::get('poli', [AuthController::class, 'poli'])->name('poli.index');
    
    Route::get('radiologi', [AuthController::class, 'radiologi'])->name('radiologi.index');
    
    Route::get('rawatInap', [AuthController::class, 'rawatInap'])->name('rawatInap.index');
    
    Route::get('rekamMedis', [AuthController::class, 'rekamMedis'])->name('rekamMedis.index');
    
    Route::get('resepObat', [AuthController::class, 'resepObat'])->name('resepObat.index');
    
    Route::get('ruangan', [AuthController::class, 'ruangan'])->name('ruangan.index');
    
    Route::get('tenagaMedis', [AuthController::class, 'tenagaMedis'])->name('tenagaMedis.index');
    
    Route::get('usersProfile', [AuthController::class, 'usersProfile'])->name('usersProfile.index');
    
    Route::get('user', [AuthController::class, 'user'])->name('user.index');
    
    Route::get('kunjunganUlang', [AuthController::class, 'kunjunganUlang'])->name('kunjunganUlang.index');
    
    Route::get('detailPembayaranPasien', [AuthController::class, 'detailPembayaranPasien'])->name('detailPembayaranPasien.index');
    
    Route::get('supplier', [AuthController::class, 'supplier'])->name('supplier.index');
    
    Route::get('pembelianObat', [AuthController::class, 'pembelianObat'])->name('pembelianObat.index');
    
    Route::get('detailPembelianObat', [AuthController::class, 'detailPembelianObat'])->name('detailPembelianObat.index');
    
    Route::get('pembayaranSupplier', [AuthController::class, 'pembayaranSupplier'])->name('pembayaranSupplier.index');
});

require __DIR__.'/auth.php';
