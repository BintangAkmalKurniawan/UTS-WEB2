<?php

namespace App\Http\Resources\Matakuliah;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class getMatakuliah extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_matakuliah' => $this->id,
            'nama_matakuliah' => $this->nama_matakuliah,
            'sks' => $this->sks,
            'id_jurusan' => $this->id_jurusan,
            'jurusan' => $this->jurusan->nama_jurusan,
        ];
    }
}
