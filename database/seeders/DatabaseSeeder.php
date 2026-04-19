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
        $jurusans = Jurusan::factory(3)->create();

        Mahasiswa::factory(3)->make()->each(function ($mahasiswa) use ($jurusans) {
            $mahasiswa->id_jurusan = $jurusans->random()->id;
            $mahasiswa->save();
        });

        Matakuliah::factory(3)->make()->each(function ($matakuliah) use ($jurusans) {
            $matakuliah->id_jurusan = $jurusans->random()->id;
            $matakuliah->save();
        });

        User::factory()->create([
            'name' => 'akmal',
            'email' => 'akmal@gmail.com',
            'password' => Hash::make('password')
        ]);
    }
}
