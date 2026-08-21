<?php

use App\Http\Controllers\Api\BiodataMahasiswaApiController;
use App\Http\Controllers\Api\JurusanApiController;
use App\Http\Controllers\Api\NilaiApiController;
use App\Http\Controllers\Api\SemesterApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API Version 1 - Terproteksi Laravel Sanctum & Rate Limiting (Maks 60 req/menit)
Route::middleware(['auth:sanctum', 'throttle:60,1'])->prefix('v1')->group(function () {
    // 1. Data Semester (Langsung semua)
    Route::get('/semesters', [SemesterApiController::class, 'index'])->name('api.v1.semesters.index');

    // 2. Data Jurusan / Program Studi (Langsung semua dengan ID Neo Feeder)
    Route::get('/jurusans', [JurusanApiController::class, 'index'])->name('api.v1.jurusans.index');

    // 3. Data Biodata & Mahasiswa (Wajib parameter id_prodi & id_periode)
    Route::get('/biodata-mahasiswa', [BiodataMahasiswaApiController::class, 'index'])->name('api.v1.biodata.index');

    // 4. Data Nilai Perkuliahan Mahasiswa (Wajib parameter id_prodi & id_semester)
    Route::get('/nilai-perkuliahan', [NilaiApiController::class, 'index'])->name('api.v1.nilai.index');
});
