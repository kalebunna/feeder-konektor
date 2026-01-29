<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mahasiswa_baru_excels', function (Blueprint $table) {
            $table->id();
            $table->string('no')->nullable();
            $table->string('nama')->nullable();
            $table->string('nim')->nullable();
            $table->string('tetala')->nullable();
            $table->string('nik')->nullable();
            $table->string('program_studi')->nullable();
            $table->string('tahun_masuk')->nullable();
            $table->string('agama')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->string('nomor_hp')->nullable();
            $table->string('rt_rw')->nullable();
            $table->string('dusun')->nullable();
            $table->string('desa_kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kabupaten')->nullable();

            // Ayah
            $table->string('nama_ayah')->nullable();
            $table->string('tetala_ayah')->nullable();
            $table->string('nik_ayah')->nullable();
            $table->string('pendidikan_ayah')->nullable();
            $table->string('rt_rw_ayah')->nullable();
            $table->string('dusun_ayah')->nullable();
            $table->string('desa_kelurahan_ayah')->nullable();
            $table->string('kecamatan_ayah')->nullable();
            $table->string('kabupaten_ayah')->nullable();
            $table->string('pekerjaan_ayah')->nullable();

            // Ibu
            $table->string('nama_ibu')->nullable();
            $table->string('tetala_ibu')->nullable();
            $table->string('nik_ibu')->nullable();
            $table->string('pendidikan_ibu')->nullable();
            $table->string('rt_rw_ibu')->nullable();
            $table->string('dusun_ibu')->nullable();
            $table->string('desa_kelurahan_ibu')->nullable();
            $table->string('kecamatan_ibu')->nullable();
            $table->string('kabupaten_ibu')->nullable();
            $table->string('nomor_hp_ibu')->nullable();

            // Wali
            $table->string('nama_wali')->nullable();
            $table->string('alamat_wali')->nullable();
            $table->string('nomor_hp_wali')->nullable();

            // Sekolah
            $table->string('asal_sekolah')->nullable();
            $table->string('alamat_sekolah')->nullable();
            $table->string('tahun_lulus')->nullable();
            $table->string('nisn')->nullable();

            $table->enum('status', ['belum sync', 'sudah sync'])->default('belum sync');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswa_baru_excels');
    }
};
