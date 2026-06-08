<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Matakuliah\getMatakuliah;
use App\Models\Matakuliah;
use Illuminate\Http\Request;

class MatakuliahApi extends Controller
{
    function data()
    {
        $jurusan = ['jurusan:id,nama_jurusan'];
        $matakuliah = Matakuliah::with($jurusan)->orderBy('id', 'desc')->get();
        if (!$matakuliah) {
            return response()->json([
                'succes' => false,
                'message' => 'Data Matakuliah Tidak Ditemukan'
            ], 404);
        }

        return response()->json([
            'succes' => true,
            'data' => getMatakuliah::collection($matakuliah),
        ], 200);
    }

    function show($id)
    {
        $matakuliah = Matakuliah::findOrFail($id);

        if (!$matakuliah) {
            return response()->json([
                'succes' => false,
                "message" => "Data Matakuliah Tidak Ditemukan"
            ]);
        }

        return response()->json([
            'succes' => true,
            'data' => new getMatakuliah($matakuliah),
        ]);
    }

    function update($id, Request $request)
    {
        $matakuliah = Matakuliah::findOrFail($id);

        if (!$matakuliah) {
            return response()->json([
                'succes' => false,
                'message' => 'Data Matakuliah Tidak Ditemukan'
            ], 404);
        }

        $matakuliah->update($request->all());
        return response()->json([
            'succes' => true,
            'data' => new getMatakuliah($matakuliah),
        ], 200);
    }

    function create(Request $request)
    {
        $matakuliah = Matakuliah::create($request->all());

        if (!$matakuliah) {
            return response()->json([
                'succes' => true,
                'message' => 'Data tida berhasil ditambahkan',
            ]);
        }

        return response()->json([
            'succes' => true,
            'data' => new getMatakuliah($matakuliah),
        ]);
    }

    function delete($id)
    {
        $matakuliah = Matakuliah::findOrFail($id);
        if (!$matakuliah) {
            return response()->json([
                'succes' => false,
                'message' => 'Data Matakuliah Tidak Ditemukan'
            ]);
        }
        $matakuliah->delete();
        return response()->json([
            'succes' => true,
            'message' => 'Data berhasil dihapus',
        ]);
    }
}
