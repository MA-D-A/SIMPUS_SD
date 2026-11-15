<?php

namespace Database\Factories;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Buku>
 */
class BukuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_buku' => 'BK-'.fake()->unique()->numerify('####'),
            'judul' => Str::title(fake()->words(3, true)),
            'pengarang' => fake()->name(),
            'penerbit' => fake()->company(),
            'tahun_terbit' => fake()->numberBetween(2005, 2026),
            'isbn' => fake()->isbn13(),
            'kategori_id' => Kategori::factory(),
            'jumlah' => fake()->numberBetween(1, 5),
        ];
    }
}
