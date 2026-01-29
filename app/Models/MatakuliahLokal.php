<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatakuliahLokal extends Model
{
    protected $table = 'matakuliah_lokal';

    protected $fillable = [
        'kode_mata_kuliah',
        'nama_mata_kuliah',
        'sks_mata_kuliah',
        'id_prodi',
        'nama_program_studi',
        'sks_tatap_muka',
        'sks_praktek',
        'sks_praktek_lapangan',
        'sks_simulasi',
        'status',
    ];

    protected $casts = [
        'sks_mata_kuliah' => 'decimal:2',
        'sks_tatap_muka' => 'decimal:2',
        'sks_praktek' => 'decimal:2',
        'sks_praktek_lapangan' => 'decimal:2',
        'sks_simulasi' => 'decimal:2',
    ];
}
