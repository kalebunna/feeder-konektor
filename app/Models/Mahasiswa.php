<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $fillable = [
        'id_registrasi_mahasiswa',
        'id_mahasiswa',
        'nim',
        'nama_mahasiswa',
        'id_prodi',
        'nama_program_studi',
        'id_agama',
        'nama_agama',
        'id_periode',
        'nama_periode_masuk',
        'tanggal_lahir',
        'jenis_kelamin',
        'nama_status_mahasiswa',
        'nipd',
        'ipk',
        'total_sks',
        'id_periode_keluar',
        'tanggal_keluar',
        'id_sms',
        'id_perguruan_tinggi',
        'status_sync'
    ];

    public function biodata()
    {
        return $this->belongsTo(BiodataMahasiswa::class, 'id_mahasiswa', 'id_mahasiswa');
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodi', 'id');
    }

    public function agama()
    {
        return $this->belongsTo(Agama::class, 'id_agama', 'id_agama');
    }

    public function periode()
    {
        return $this->belongsTo(TahunAjaran::class, 'id_periode', 'id_tahun_ajaran');
    }
}
