<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusMahasiswa extends Model
{
    protected $table = 'status_mahasiswa';
    protected $primaryKey = 'id_status_mahasiswa';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_status_mahasiswa',
        'nama_status_mahasiswa',
    ];
}
