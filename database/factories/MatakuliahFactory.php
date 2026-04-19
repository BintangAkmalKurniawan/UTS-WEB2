<?php

namespace Database\Factories;

use App\Models\Jurusan;
use App\Models\Matakuliah;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Matakuliah>
 */
class MatakuliahFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_matakuliah' => $this->faker->name(),
            'id_jurusan' => Jurusan::query()->inRandomOrder()->value('id') ?? Jurusan::factory(),
            'sks' => $this->faker->numberBetween(1, 8),
        ];
    }
}
