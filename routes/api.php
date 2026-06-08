<?php

use App\Http\Controllers\Api\JurusanApi;
use App\Http\Controllers\Api\MahasiswaApi;
use App\Http\Controllers\Api\MatakuliahApi;
use Illuminate\Support\Facades\Route;

Route::get('/mahasiswa', [MahasiswaApi::class, 'data']);
Route::get('/mahasiswa/{id}', [MahasiswaApi::class, 'show']);
Route::post('/mahasiswa', [MahasiswaApi::class, 'create']);
Route::put('/mahasiswa/{id}', [MahasiswaApi::class, 'edit']);
Route::delete('/mahasiswa/{id}', [MahasiswaApi::class, 'delete']);

Route::get('/jurusan', [JurusanApi::class, 'data']);
Route::get('/jurusan/{id}', [JurusanApi::class, 'show']);
Route::post('/jurusan', [JurusanApi::class, 'create']);
Route::put('/jurusan/{id}', [JurusanApi::class, 'edit']);
Route::delete('/jurusan/{id}', [JurusanApi::class, 'delete']);

Route::get('/matakuliah', [MatakuliahApi::class, 'data']);
Route::get('/matakuliah/{id}', [MatakuliahApi::class, 'show']);
Route::post('/matakuliah', [MatakuliahApi::class, 'create']);
Route::put('/matakuliah/{id}', [MatakuliahApi::class, 'update']);
Route::delete('/matakuliah/{id}', [MatakuliahApi::class, 'delete']);
