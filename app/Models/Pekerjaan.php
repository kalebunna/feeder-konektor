<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pekerjaan extends Model
{
    protected $table = 'pekerjaan';
    protected $primaryKey = 'id_pekerjaan';
    public $incrementing = false;
    protected $keyType = 'integer';

    protected $fillable = [
        'id_pekerjaan',
        'nama_pekerjaan',
    ];
}
