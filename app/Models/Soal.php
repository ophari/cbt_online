<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    protected $fillable = [
        'kategori',
        'pertanyaan',
        'gambar',
        'jawaban_a',
        'jawaban_b',
        'jawaban_c',
        'jawaban_d',
        'jawaban_e',
        'kunci_jawaban',
    ];

    public function jawaban()
    {
        return $this->hasMany(Jawaban::class, 'id_soal', 'id');
    }
}
