<?php

namespace Database\Factories;

use App\Models\Anggota;
use App\Models\Peminjaman;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Peminjaman>
 */
class PeminjamanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'anggota_id' => Anggota::factory(),
            'tanggal_pinjam' => today(),
            'tanggal_jatuh_tempo' => today()->addDays(7),
            'status' => 'dipinjam',
        ];
    }

    /**
     * Dipinjam $hari hari lalu dengan lama pinjam 7 hari.
     */
    public function sejak(int $hari): static
    {
        return $this->state(fn () => [
            'tanggal_pinjam' => today()->subDays($hari),
            'tanggal_jatuh_tempo' => today()->subDays($hari)->addDays(7),
        ]);
    }
}
