<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Mahasiswa\getMahasiswa;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class MahasiswaApi extends Controller
{
    function data()
    {
        $jurusan = ['jurusan:id,nama_jurusan'];
        $mahasiswa = Mahasiswa::with($jurusan)->orderBy('id', 'desc')->get();
        if (!$mahasiswa) {
            return response()->json([
                'succes' => false,
                'message' => 'Data Mahasiswa Tidak Ditemukan'
            ], 404);
        }
        // return response()->json([
        //     'succes' => true,
        //     'data' => $mahasiswa
        // ], 200);

        return getMahasiswa::collection($mahasiswa);
    }

    function show($id)
    {
        $mahasiswa = Mahasiswa::with('jurusan')->find($id);

        if (!$mahasiswa) {
            return response()->json([
                'succes' => false,
                'message' => 'Data Mahasiswa Tidak Ditemukan'
            ], 404);
        }

        return response()->json([
            'succes' => true,
            'data' => new getMahasiswa($mahasiswa),
        ]);
    }

    function create(Request $request)
    {
        $mahasiswa = Mahasiswa::create($request->all());

        return response()->json([
            'succes' => true,
            'data' => new getMahasiswa($mahasiswa),
        ]);
    }

    function edit(Request $request, $id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);

        if (!$mahasiswa) {
            return response()->json([
                'succes' => false,
                'message' => 'Data Mahasiswa Tidak Ditemukan'
            ], 404);
        }

        $mahasiswa->update($request->all());

        return response()->json([
            'succes' => true,
            'data' => new getMahasiswa($mahasiswa),
        ], 200);
    }

    function delete($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);

        if (!$mahasiswa) {
            return response()->json([
                'succes' => false,
                'message' => 'Data Mahasiswa Tidak Ditemukan'
            ], 404);
        }

        $mahasiswa->delete();

        return response()->json([
            'succes' => true,
            'message' => 'Data Mahasiswa Berhasil Dihapus',
        ], 200);
    }
}
