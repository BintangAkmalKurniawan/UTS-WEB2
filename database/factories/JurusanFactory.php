<?php

namespace Database\Factories;

use App\Models\Jurusan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Jurusan>
 */
class JurusanFactory extends Factory
{
    private const NAMA_JURUSAN = [
        'Teknik Informatika',
        'Sistem Informasi',
        'Manajemen Informatika',
        'Teknik Komputer',
        'Desain Komunikasi Visual',
        'Teknik Elektro',
        'Teknik Industri',
        'Akuntansi',
        'Manajemen',
        'Bisnis Digital',
        'Ilmu Komunikasi',
        'Administrasi Bisnis',
        'Teknik Sipil',
        'Arsitektur',
        'Pendidikan Matematika',
        'Pendidikan Bahasa Inggris',
        'Hukum',
        'Psikologi',
        'Keperawatan',
        'Farmasi',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_jurusan' => $this->faker->unique()->randomElement(self::NAMA_JURUSAN),
            'akreditasi' => $this->faker->randomElement(['A', 'B', 'AB', 'C']),
        ];
    }
}
