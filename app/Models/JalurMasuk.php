<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JalurMasuk extends Model
{
    protected $table = 'jalur_masuk';
    protected $primaryKey = 'id_jalur_masuk';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_jalur_masuk',
        'nama_jalur_masuk',
    ];
}
