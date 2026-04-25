<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoalAcak extends Model
{
    protected $fillable = [
        'id_siswa',
        'id_soal',
        'urutan',
        'tahap',
        'opsi_map',
    ];

    protected $casts = [
        'opsi_map' => 'array',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'id_siswa', 'id');
    }

    public function soal()
    {
        return $this->belongsTo(Soal::class, 'id_soal', 'id');
    }
}
