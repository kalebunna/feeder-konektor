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
        Schema::table('mahasiswa_baru_excels', function (Blueprint $table) {
            $table->string('tempat_lahir_ayah')->nullable()->after('tetala_ayah');
            $table->date('tanggal_lahir_ayah')->nullable()->after('tempat_lahir_ayah');
            $table->string('id_penghasilan_ayah')->nullable()->after('pekerjaan_ayah');

            $table->string('tempat_lahir_ibu')->nullable()->after('tetala_ibu');
            $table->date('tanggal_lahir_ibu')->nullable()->after('tempat_lahir_ibu');
            $table->string('id_penghasilan_ibu')->nullable()->after('kabupaten_ibu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswa_baru_excels', function (Blueprint $table) {
            $table->dropColumn([
                'tempat_lahir_ayah',
                'tanggal_lahir_ayah',
                'id_penghasilan_ayah',
                'tempat_lahir_ibu',
                'tanggal_lahir_ibu',
                'id_penghasilan_ibu'
            ]);
        });
    }
};
