<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisKeluar extends Model
{
    protected $table = 'jenis_keluar';
    protected $primaryKey = 'id_jenis_keluar';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_jenis_keluar',
        'jenis_keluar',
        'apa_mahasiswa',
    ];
}
