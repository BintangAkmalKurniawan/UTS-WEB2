<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;
    protected $table = 'jurusan';
    protected $guarded = [];

    public function mahasiswa()
    {
        return $this->hasMany(Mahasiswa::class, 'id_jurusan', 'id');
    }

    public function matakuliah()
    {
        return $this->hasMany(Matakuliah::class, 'id_jurusan', 'id');
    }
}
