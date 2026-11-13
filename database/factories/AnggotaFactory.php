<?php

namespace Database\Factories;

use App\Models\Anggota;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Anggota>
 */
class AnggotaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jenisKelamin = fake()->randomElement(['L', 'P']);
        $gender = $jenisKelamin === 'L' ? 'male' : 'female';

        return [
            'nomor_induk' => fake()->unique()->numerify('20#####'),
            // firstName + lastName: name() kadang menambah gelar (mis. "S.H.") yang tidak cocok untuk siswa SD.
            'nama' => fake()->firstName($gender).' '.fake()->lastName($gender),
            'jenis' => 'siswa',
            'kelas' => fake()->numberBetween(1, 6).fake()->randomElement(['A', 'B']),
            'jenis_kelamin' => $jenisKelamin,
            'no_hp' => fake()->numerify('08##########'),
            'aktif' => true,
        ];
    }

    public function guru(): static
    {
        return $this->state(fn (array $data) => [
            'nomor_induk' => fake()->unique()->numerify('19##############'),
            'nama' => $data['nama'].', S.Pd.',
            'jenis' => 'guru',
            'kelas' => null,
        ]);
    }

    public function nonaktif(): static
    {
        return $this->state(['aktif' => false]);
    }
}
