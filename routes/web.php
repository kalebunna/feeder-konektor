<?php

Auth::routes(['register' => false]);

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('prodi.index');
    });

    Route::get('/prodi', [\App\Http\Controllers\ProdiController::class, 'index'])->name('prodi.index');
    Route::get('/profil-pt', [\App\Http\Controllers\ProfilPTController::class, 'index'])->name('profil-pt.index');

    Route::get('/test-feeder', [\App\Http\Controllers\FeederTestController::class, 'showTestPage'])->name('feeder.test');
    Route::post('/test-feeder/submit', [\App\Http\Controllers\FeederTestController::class, 'submitRequest'])->name('feeder.test.submit');

    Route::get('/admin/reference', [\App\Http\Controllers\ReferenceController::class, 'index'])->name('reference.index');
    Route::post('/admin/reference/sync/{table}', [\App\Http\Controllers\ReferenceController::class, 'sync'])->name('reference.sync');

    Route::get('/admin/biodata-mahasiswa', [\App\Http\Controllers\BiodataMahasiswaController::class, 'index'])->name('biodata-mahasiswa.index');
    Route::get('/admin/biodata-mahasiswa/{id}', [\App\Http\Controllers\BiodataMahasiswaController::class, 'show'])->name('biodata-mahasiswa.show');
    Route::post('/admin/biodata-mahasiswa/sync', [\App\Http\Controllers\BiodataMahasiswaController::class, 'sync'])->name('biodata-mahasiswa.sync');

    Route::get('/admin/mahasiswa', [\App\Http\Controllers\MahasiswaController::class, 'index'])->name('mahasiswa.index');
    Route::post('/admin/mahasiswa/sync', [\App\Http\Controllers\MahasiswaController::class, 'sync'])->name('mahasiswa.sync');

    Route::get('/admin/kelas-kuliah', [\App\Http\Controllers\KelasKuliahController::class, 'index'])->name('kelas-kuliah.index');
    Route::get('/admin/kelas-kuliah/create', [\App\Http\Controllers\KelasKuliahController::class, 'create'])->name('kelas-kuliah.create');
    Route::post('/admin/kelas-kuliah/store', [\App\Http\Controllers\KelasKuliahController::class, 'store'])->name('kelas-kuliah.store');
    Route::get('/admin/kelas-kuliah/ajax/kurikulum-detail', [\App\Http\Controllers\KelasKuliahController::class, 'getDetailKurikulum'])->name('kelas-kuliah.ajax.kurikulum-detail');
    Route::get('/admin/kelas-kuliah/ajax/matkul-kurikulum', [\App\Http\Controllers\KelasKuliahController::class, 'getMatkulKurikulum'])->name('kelas-kuliah.ajax.matkul-kurikulum');

    Route::get('/admin/tahun-ajaran', [\App\Http\Controllers\TahunAjaranController::class, 'index'])->name('tahun-ajaran.index');
    Route::post('/admin/tahun-ajaran/sync', [\App\Http\Controllers\TahunAjaranController::class, 'sync'])->name('tahun-ajaran.sync');
    Route::post('/admin/tahun-ajaran/sync-active', [\App\Http\Controllers\TahunAjaranController::class, 'syncActiveStatus'])->name('tahun-ajaran.sync-active');

    Route::get('/admin/semester', [\App\Http\Controllers\SemesterController::class, 'index'])->name('semester.index');
    Route::post('/admin/semester/sync', [\App\Http\Controllers\SemesterController::class, 'sync'])->name('semester.sync');
    Route::post('/admin/semester/sync-active', [\App\Http\Controllers\SemesterController::class, 'syncActiveStatus'])->name('semester.sync-active');

    Route::get('/admin/import-mahasiswa', [\App\Http\Controllers\ImportMahasiswaController::class, 'index'])->name('import-mahasiswa.index');
    Route::post('/admin/import-mahasiswa', [\App\Http\Controllers\ImportMahasiswaController::class, 'import'])->name('import-mahasiswa.import');
    Route::get('/admin/import-mahasiswa/sync/{id}', [\App\Http\Controllers\ImportMahasiswaController::class, 'syncForm'])->name('import-mahasiswa.sync-form');
    Route::post('/admin/import-mahasiswa/sync/{id}', [\App\Http\Controllers\ImportMahasiswaController::class, 'syncProcess'])->name('import-mahasiswa.sync-process');

    Route::prefix('admin/matakuliah-lokal')->name('matakuliah-lokal.')->group(function () {
        Route::get('/', [\App\Http\Controllers\MatakuliahLokalController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\MatakuliahLokalController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\MatakuliahLokalController::class, 'show'])->name('show');
        Route::put('/{id}', [\App\Http\Controllers\MatakuliahLokalController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\MatakuliahLokalController::class, 'destroy'])->name('destroy');
        Route::post('/import', [\App\Http\Controllers\MatakuliahLokalController::class, 'import'])->name('import');
        Route::post('/{id}/sync', [\App\Http\Controllers\MatakuliahLokalController::class, 'sync'])->name('sync');
        Route::post('/bulk-sync', [\App\Http\Controllers\MatakuliahLokalController::class, 'bulkSync'])->name('bulk-sync');
    });

    Route::get('/admin/kurikulum', [\App\Http\Controllers\KurikulumController::class, 'index'])->name('kurikulum.index');
    Route::get('/admin/kurikulum/{id}', [\App\Http\Controllers\KurikulumController::class, 'show'])->name('kurikulum.show');
    Route::delete('/admin/kurikulum/matkul/destroy', [\App\Http\Controllers\KurikulumController::class, 'deleteMatkulKurikulum'])->name('kurikulum.matkul.destroy');
    Route::get('/admin/kurikulum/ajax/matkul-list', [\App\Http\Controllers\KurikulumController::class, 'getMatakuliahList'])->name('kurikulum.ajax.matkul-list');
    Route::post('/admin/kurikulum/matkul/store-bulk', [\App\Http\Controllers\KurikulumController::class, 'storeMatkulKurikulumBulk'])->name('kurikulum.matkul.store-bulk');
    Route::get('/admin/kurikulum/ajax/kurikulum-list', [\App\Http\Controllers\KurikulumController::class, 'getKurikulumList'])->name('kurikulum.ajax.list');
    Route::post('/admin/kurikulum/matkul/copy', [\App\Http\Controllers\KurikulumController::class, 'copyMatkulKurikulum'])->name('kurikulum.matkul.copy');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
