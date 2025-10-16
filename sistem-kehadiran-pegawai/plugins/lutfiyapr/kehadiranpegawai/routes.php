<?php

use Illuminate\Support\Facades\Route;
use Lutfiyapr\KehadiranPegawai\Controllers\AuthController;
use Lutfiyapr\KehadiranPegawai\Controllers\PegawaiController;
use Lutfiyapr\KehadiranPegawai\Controllers\PresensiController;

Route::prefix('api')->group(function () {

    // ========== AUTH ROUTES (Public) ==========
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/change-password', [AuthController::class, 'changePassword']);
    });

    // ========== PROTECTED ROUTES ==========
    Route::middleware([\Lutfiyapr\KehadiranPegawai\Middleware\Authenticate::class])->group(function () {

        // Routes untuk Pegawai (Admin only untuk create, update, delete)
        Route::prefix('pegawai')->group(function () {
            Route::get('/', [PegawaiController::class, 'index']);
            Route::get('/{id}', [PegawaiController::class, 'show']);

            // Admin only
            Route::middleware([\Lutfiyapr\KehadiranPegawai\Middleware\Authenticate::class . ':admin'])->group(function () {
                Route::post('/', [PegawaiController::class, 'store']);
                Route::put('/{id}', [PegawaiController::class, 'update']);
                Route::delete('/{id}', [PegawaiController::class, 'destroy']);
            });
        });

        // Routes untuk Presensi
        Route::prefix('presensi')->group(function () {
            Route::get('/', [PresensiController::class, 'index']);
            Route::get('/{id}', [PresensiController::class, 'show']);

            // Presensi Masuk & Pulang (Semua user yang login)
            Route::post('/masuk', [PresensiController::class, 'masuk']);
            Route::post('/pulang', [PresensiController::class, 'pulang']);

            // Riwayat & Status
            Route::get('/pegawai/{pegawai_id}', [PresensiController::class, 'riwayatPegawai']);
            Route::get('/status/{pegawai_id}', [PresensiController::class, 'statusHariIni']);
        });
    });
});
