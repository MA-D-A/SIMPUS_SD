<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Pengaturan;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Data awal saat instalasi. Tidak memakai factory/Faker karena
     * di komputer sekolah dependency dev tidak dipasang (--no-dev).
     */
    public function run(): void
    {
        User::create([
            'name' => 'Petugas Perpustakaan',
            'username' => 'admin',
            'password' => 'admin123',
        ]);

        Pengaturan::ambil();

        foreach (['Fiksi','Non-Fiksi'] as $nama) {
            Kategori::create(['nama' => $nama]);
        }
    }
}
