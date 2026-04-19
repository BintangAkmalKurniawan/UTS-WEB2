<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use App\Models\Mahasiswa;
use App\Models\Matakuliah;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $jurusans = collect([
            ['nama_jurusan' => 'Teknik Informatika', 'akreditasi' => 'A'],
            ['nama_jurusan' => 'Sistem Informasi', 'akreditasi' => 'AB'],
            ['nama_jurusan' => 'Manajemen Informatika', 'akreditasi' => 'B'],
        ])->map(function (array $jurusan) {
            return Jurusan::updateOrCreate(
                ['nama_jurusan' => $jurusan['nama_jurusan']],
                ['akreditasi' => $jurusan['akreditasi']],
            );
        });

        Mahasiswa::factory(3)->make()->each(function ($mahasiswa) use ($jurusans) {
            $mahasiswa->id_jurusan = $jurusans->random()->id;
            $mahasiswa->save();
        });

        Matakuliah::factory(3)->make()->each(function ($matakuliah) use ($jurusans) {
            $matakuliah->id_jurusan = $jurusans->random()->id;
            $matakuliah->save();
        });

        User::updateOrCreate([
            'email' => 'akmal@gmail.com',
        ], [
            'name' => 'akmal',
            'password' => Hash::make('password')
        ]);
    }
}
