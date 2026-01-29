<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agama extends Model
{
    protected $primaryKey = 'id_agama';
    public $incrementing = false;
    protected $keyType = 'integer';

    protected $fillable = [
        'id_agama',
        'nama_agama',
    ];
}
