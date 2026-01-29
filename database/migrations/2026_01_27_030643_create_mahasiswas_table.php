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
        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_registrasi_mahasiswa')->nullable()->index();
            $table->uuid('id_mahasiswa');
            $table->string('nim')->nullable();
            $table->string('nama_mahasiswa');
            $table->uuid('id_prodi')->nullable();
            $table->string('nama_program_studi')->nullable();
            $table->integer('id_agama')->nullable();
            $table->string('nama_agama')->nullable();
            $table->string('id_periode', 5)->nullable();
            $table->string('nama_periode_masuk')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin', 1)->nullable();
            $table->string('nama_status_mahasiswa')->nullable();
            $table->string('nipd')->nullable();
            $table->decimal('ipk', 4, 2)->nullable();
            $table->integer('total_sks')->nullable();
            $table->string('id_periode_keluar', 5)->nullable();
            $table->date('tanggal_keluar')->nullable();
            $table->uuid('id_sms')->nullable();
            $table->uuid('id_perguruan_tinggi')->nullable();
            $table->string('status_sync')->default('belum sync');
            $table->timestamps();

            // Relations
            $table->foreign('id_mahasiswa')->references('id_mahasiswa')->on('biodata_mahasiswas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswas');
    }
};
