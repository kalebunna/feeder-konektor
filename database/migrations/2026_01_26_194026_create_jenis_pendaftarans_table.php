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
        Schema::create('jenis_pendaftaran', function (Blueprint $table) {
            $table->string('id_jenis_daftar')->primary();
            $table->string('nama_jenis_daftar');
            $table->string('untuk_daftar_sekolah')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_pendaftarans');
    }
};
