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
        Schema::create('matakuliah_lokal', function (Blueprint $table) {
            $table->id();
            $table->string('kode_mata_kuliah');
            $table->string('nama_mata_kuliah');
            $table->decimal('sks_mata_kuliah', 5, 2);
            $table->uuid('id_prodi');
            $table->string('nama_program_studi');
            $table->decimal('sks_tatap_muka', 5, 2);
            $table->decimal('sks_praktek', 5, 2);
            $table->decimal('sks_praktek_lapangan', 5, 2);
            $table->decimal('sks_simulasi', 5, 2);
            $table->enum('status', ['sudah sync', 'belum sync'])->default('belum sync');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matakuliah_lokal');
    }
};
