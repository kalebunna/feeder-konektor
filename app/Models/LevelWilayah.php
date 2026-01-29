<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LevelWilayah extends Model
{
    protected $table = 'level_wilayah';
    protected $primaryKey = 'id_level_wilayah';
    public $incrementing = false;
    protected $keyType = 'integer';

    protected $fillable = [
        'id_level_wilayah',
        'nama_level_wilayah',
    ];
}
