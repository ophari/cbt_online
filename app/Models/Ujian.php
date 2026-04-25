<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ujian extends Model
{
    protected $fillable = [
        'id_siswa',
        'nisn',
        'status',
        'tahap',
        'mulai_at',
        'waktu_selesai_umum',
        'selesai_at',
        'session_id',
    ];

    protected $casts = [
        'mulai_at' => 'datetime',
        'waktu_selesai_umum' => 'datetime',
        'selesai_at' => 'datetime',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'id_siswa', 'id');
    }
}
