<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    protected $table = 'tahun_ajarans';
    protected $primaryKey = 'id_tahun_ajaran';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_tahun_ajaran',
        'nama_tahun_ajaran',
        'a_periode_aktif',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function semesters()
    {
        return $this->hasMany(Semester::class, 'id_tahun_ajaran', 'id_tahun_ajaran');
    }
}
