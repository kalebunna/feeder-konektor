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
        Schema::create('skala_nilais', function (Blueprint $table) {
            $table->uuid('id_bobot_nilai')->primary();
            $table->uuid('id_prodi')->nullable();
            $table->string('nama_program_studi')->nullable();
            $table->string('nilai_huruf', 3)->nullable();
            $table->decimal('nilai_indeks', 4, 2)->nullable();
            $table->decimal('bobot_nilai_min', 5, 2)->nullable();
            $table->decimal('bobot_nilai_maks', 5, 2)->nullable();
            $table->date('tanggal_mulai_efektif')->nullable();
            $table->date('tanggal_akhir_efektif')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skala_nilais');
    }
};
