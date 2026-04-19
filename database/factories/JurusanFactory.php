<?php

namespace Database\Factories;

use App\Models\Jurusan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Jurusan>
 */
class JurusanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_jurusan' => $this->faker->randomElement([
                'Teknik Informatika',
                'Sistem Informasi',
                'Manajemen Informatika',
                'Teknik Komputer',
                'Desain Komunikasi Visual',
            ]),
            'akreditasi' => $this->faker->randomElement(['A', 'B', 'AB', 'C']),
        ];
    }
}
