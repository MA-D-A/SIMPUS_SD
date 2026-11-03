<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Table('pengaturan')]
#[Fillable(['nama_sekolah', 'alamat_sekolah', 'nama_kepala_sekolah', 'nip_kepala_sekolah', 'lama_pinjam', 'maks_pinjam'])]
class Pengaturan extends Model
{
    /**
     * Satu-satunya baris pengaturan; dibuat dengan nilai bawaan jika belum ada.
     */
    public static function ambil(): self
    {
        return static::query()->firstOrCreate([], [
            'nama_sekolah' => 'SD Negeri 5 Waygalih',
            'lama_pinjam' => 7,
            'maks_pinjam' => 2  ,
        ]);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'lama_pinjam' => 'integer',
            'maks_pinjam' => 'integer',
        ];
    }
}
