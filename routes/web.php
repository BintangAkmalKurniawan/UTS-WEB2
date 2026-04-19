<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages.login')->name('login');
Route::middleware('auth')->group(function () {
                    Route::livewire('/admin/dashboard', 'pages.dashboard')->name('dashboard');
                    Route::livewire('/admin/tambah-mahasiswa', 'pages.tambah-mahasiswa')->name('tambah-mahasiswa');
                    Route::livewire('/admin/mahasiswa', 'pages.data-mahasiswa')->name('data-mahasiswa');
                    Route::livewire('/admin/jurusan', 'pages.tambah-jurusan')->name('tambah-jurusan');
                    Route::livewire('/admin/matakuliah', 'pages.matakuliah')->name('matakuliah');
});
