<?php

namespace Database\Factories;

use App\Models\Jurusan;
use App\Models\Mahasiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mahasiswa>
 */
class MahasiswaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nim' => $this->faker->unique()->numerify('##########'),
            'nama' => $this->faker->name(),
            'id_jurusan' => Jurusan::query()->inRandomOrder()->value('id') ?? Jurusan::factory(),
        ];
    }
}
