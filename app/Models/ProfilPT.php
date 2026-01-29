<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ProfilPT extends Model
{
    use HasUuids;

    protected $table = 'profil_pts';

    protected $fillable = [
        'id_perguruan_tinggi',
        'kode_perguruan_tinggi',
        'nama_perguruan_tinggi',
        'telepon',
        'faximile',
        'email',
        'website',
        'jalan',
        'dusun',
        'rt_rw',
        'kelurahan',
        'kode_pos',
        'id_wilayah',
        'nama_wilayah',
        'lintang_bujur',
        'bank',
        'unit_cabang',
        'nomor_rekening',
        'mbs',
        'luas_tanah_milik',
        'luas_tanah_bukan_milik',
    ];
}
