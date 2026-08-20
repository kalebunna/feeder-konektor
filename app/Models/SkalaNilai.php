<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkalaNilai extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_bobot_nilai';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_bobot_nilai',
        'id_prodi',
        'nama_program_studi',
        'nilai_huruf',
        'nilai_indeks',
        'bobot_nilai_min',
        'bobot_nilai_maks',
        'tanggal_mulai_efektif',
        'tanggal_akhir_efektif',
    ];

    public function setTanggalMulaiEfektifAttribute($value)
    {
        if ($value) {
            $this->attributes['tanggal_mulai_efektif'] = date('Y-m-d', strtotime($value));
        } else {
            $this->attributes['tanggal_mulai_efektif'] = null;
        }
    }

    public function setTanggalAkhirEfektifAttribute($value)
    {
        if ($value) {
            $this->attributes['tanggal_akhir_efektif'] = date('Y-m-d', strtotime($value));
        } else {
            $this->attributes['tanggal_akhir_efektif'] = null;
        }
    }
}
