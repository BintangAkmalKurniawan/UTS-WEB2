<?php

namespace App\Http\Resources\Mahasiswa;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class getMahasiswa extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_mahasiswa' => $this->id,
            'nim' => $this->nim,
            'nama' => $this->nama,
            'id_jurusan' => $this->id_jurusan,
            'jurusan' => $this->jurusan,
            // 'nama_kamu' => $this->jurusan->nama_jurusan,
            // 'nilai_kampus' => $this->jurusan->akreditasi
        ];
    }
}
