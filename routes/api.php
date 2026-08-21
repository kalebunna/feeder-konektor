<?php

use App\Http\Controllers\Api\BiodataMahasiswaApiController;
use App\Http\Controllers\Api\JurusanApiController;
use App\Http\Controllers\Api\SemesterApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API Version 1 - Terproteksi Laravel Sanctum & Rate Limiting (Maks 60 req/menit)
Route::middleware(['auth:sanctum', 'throttle:60,1'])->prefix('v1')->group(function () {
    // 1. Data Semester
    Route::get('/semesters', [SemesterApiController::class, 'index'])->name('api.v1.semesters.index');
    Route::get('/semesters/{id}', [SemesterApiController::class, 'show'])->name('api.v1.semesters.show');

    // 2. Data Jurusan / Program Studi
    Route::get('/jurusans', [JurusanApiController::class, 'index'])->name('api.v1.jurusans.index');
    Route::get('/jurusans/{id}', [JurusanApiController::class, 'show'])->name('api.v1.jurusans.show');

    // 3. Data Biodata & Status Mahasiswa (dengan ID Neo Feeder lengkap)
    Route::get('/biodata-mahasiswa', [BiodataMahasiswaApiController::class, 'index'])->name('api.v1.biodata.index');
    Route::get('/biodata-mahasiswa/{id}', [BiodataMahasiswaApiController::class, 'show'])->name('api.v1.biodata.show');
});

