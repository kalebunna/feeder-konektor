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
        Schema::create('biodata_mahasiswas', function (Blueprint $table) {
            $table->uuid('id_mahasiswa')->primary();
            $table->string('nama_mahasiswa');
            $table->string('jenis_kelamin', 1);
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->integer('id_agama');
            $table->string('nama_agama');
            $table->string('nik', 25);
            $table->string('nisn', 10)->nullable();
            $table->string('npwp', 16)->nullable();
            $table->string('id_negara', 2);
            $table->string('kewarganegaraan');
            $table->string('jalan')->nullable();
            $table->string('dusun')->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('id_wilayah', 10);
            $table->string('nama_wilayah');
            $table->string('id_jenis_tinggal', 5)->nullable();
            $table->string('nama_jenis_tinggal')->nullable();
            $table->string('id_alat_transportasi', 5)->nullable();
            $table->string('nama_alat_transportasi')->nullable();
            $table->string('telepon')->nullable();
            $table->string('handphone')->nullable();
            $table->string('email')->nullable();
            $table->string('penerima_kps', 1)->default('0');
            $table->string('nomor_kps')->nullable();
            $table->string('nik_ayah', 25)->nullable();
            $table->string('nama_ayah')->nullable();
            $table->date('tanggal_lahir_ayah')->nullable();
            $table->string('id_pendidikan_ayah')->nullable();
            $table->string('nama_pendidikan_ayah')->nullable();
            $table->string('id_pekerjaan_ayah')->nullable();
            $table->string('nama_pekerjaan_ayah')->nullable();
            $table->string('id_penghasilan_ayah')->nullable();
            $table->string('nama_penghasilan_ayah')->nullable();
            $table->string('nik_ibu', 25)->nullable();
            $table->string('nama_ibu_kandung')->nullable();
            $table->date('tanggal_lahir_ibu')->nullable();
            $table->string('id_pendidikan_ibu')->nullable();
            $table->string('nama_pendidikan_ibu')->nullable();
            $table->string('id_pekerjaan_ibu')->nullable();
            $table->string('nama_pekerjaan_ibu')->nullable();
            $table->string('id_penghasilan_ibu')->nullable();
            $table->string('nama_penghasilan_ibu')->nullable();
            $table->string('nama_wali')->nullable();
            $table->date('tanggal_lahir_wali')->nullable();
            $table->string('id_pendidikan_wali')->nullable();
            $table->string('nama_pendidikan_wali')->nullable();
            $table->string('id_pekerjaan_wali')->nullable();
            $table->string('nama_pekerjaan_wali')->nullable();
            $table->string('id_penghasilan_wali')->nullable();
            $table->string('nama_penghasilan_wali')->nullable();
            $table->integer('id_kebutuhan_khusus_mahasiswa')->default(0);
            $table->string('nama_kebutuhan_khusus_mahasiswa')->nullable();
            $table->integer('id_kebutuhan_khusus_ayah')->default(0);
            $table->string('nama_kebutuhan_khusus_ayah')->nullable();
            $table->integer('id_kebutuhan_khusus_ibu')->default(0);
            $table->string('nama_kebutuhan_khusus_ibu')->nullable();
            $table->string('status_sync')->default('belum sync');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biodata_mahasiswas');
    }
};
