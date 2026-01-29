<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IkatanKerja extends Model
{
    protected $table = 'ikatan_kerja';
    protected $primaryKey = 'id_ikatan_kerja';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_ikatan_kerja',
        'nama_ikatan_kerja',
    ];
}
