<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPendaftaran extends Model
{
    protected $table = 'jenis_pendaftaran';
    protected $primaryKey = 'id_jenis_daftar';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_jenis_daftar',
        'nama_jenis_daftar',
        'untuk_daftar_sekolah',
    ];
}
