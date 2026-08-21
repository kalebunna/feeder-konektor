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
        'id_periode_masuk',
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

    public function setIdPeriodeMasukAttribute($value)
    {
        $this->attributes['id_periode'] = $value;
    }

    public function getIdPeriodeMasukAttribute()
    {
        return $this->attributes['id_periode'] ?? null;
    }

    public function biodata()
    {
        return $this->belongsTo(BiodataMahasiswa::class, 'id_mahasiswa', 'id_mahasiswa');
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodi', 'id_prodi');
    }

    public function agama()
    {
        return $this->belongsTo(Agama::class, 'id_agama', 'id_agama');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'id_periode', 'id_semester');
    }

    public function periode()
    {
        return $this->belongsTo(Semester::class, 'id_periode', 'id_semester');
    }

    public function tahunAjaran()
    {
        return $this->hasOneThrough(
            TahunAjaran::class,
            Semester::class,
            'id_semester',      // Foreign key on semesters table
            'id_tahun_ajaran',  // Foreign key on tahun_ajarans table
            'id_periode',       // Local key on mahasiswas table
            'id_tahun_ajaran'   // Local key on semesters table
        );
    }
}
