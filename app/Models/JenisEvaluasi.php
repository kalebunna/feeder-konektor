<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisEvaluasi extends Model
{
    protected $table = 'jenis_evaluasi';
    protected $primaryKey = 'id_jenis_evaluasi';
    public $incrementing = false;
    protected $keyType = 'integer';

    protected $fillable = [
        'id_jenis_evaluasi',
        'nama_jenis_evaluasi',
    ];
}
