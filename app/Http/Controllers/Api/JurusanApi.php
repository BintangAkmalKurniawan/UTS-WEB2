<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Jurusan\getJurusan;
use App\Models\Jurusan;
use Illuminate\Http\Request;

class JurusanApi extends Controller
{
    function data()
    {
        $jurusan = Jurusan::select('id', 'nama_jurusan', 'akreditasi')->get();

        if ($jurusan->isEmpty()) {
            return response()->json([
                'succes' => false,
                'message' => 'Data Jurusan Tidak Ditemukan'
            ], 404);
        }

        return response()->json([
            'succes' => true,
            'message' => 'Data Jurusan Berhasil Ditemukan',
            'data' => getJurusan::collection($jurusan),
        ]);
    }

    function show($id)
    {
        $jurusan = Jurusan::findOrFail($id);

        if (!$jurusan) {
            return response()->json([
                'succes' => false,
                'message' => 'Data Jurusan Tidak Ditemukan'
            ], 404);
        }

        return response()->json([
            'succes' => true,
            'message' => 'Data Jurusan Berhasil Ditemukan',
            'data' => new getJurusan($jurusan),
        ]);
    }

    function create(Request $request)
    {
        $jurusan  = Jurusan::create($request->all());

        return response()->json([
            'succes' => true,
            'message' => 'Data Jurusan Berhasil Ditambahkan',
            'data' => new getJurusan($jurusan),
        ]);
    }

    function edit($id, Request $request)
    {
        $jurusan = Jurusan::findOrFail($id);

        if (!$jurusan) {
            return response()->json([
                'succes' => false,
                'message' => 'Data Jurusan Tidak Ditemukan'
            ], 404);
        }

        $jurusan->update($request->all());

        return response()->json([
            'succes' => true,
            'message' => 'Data Jurusan Berhasil Diubah',
            'data' => new getJurusan($jurusan),
        ]);
    }

    function delete($id)
    {
        $jurusan = Jurusan::findOrFail($id);

        if (!$jurusan) {
            return response()->json([
                'succes' => false,
                'message' => 'Data Jurusan Tidak Ditemukan'
            ], 404);
        }

        $jurusan->delete();

        return response()->json([
            'succes' => true,
            'message' => 'Data Jurusan Berhasil Dihapus',
        ]);
    }
}
