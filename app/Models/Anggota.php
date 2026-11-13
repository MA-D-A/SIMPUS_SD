<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[Table('anggota')]
#[Fillable(['nomor_induk', 'nama', 'jenis', 'kelas', 'jenis_kelamin', 'no_hp', 'aktif'])]
class Anggota extends Model
{
    use HasFactory;

    /** @return HasMany<Peminjaman, $this> */
    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class);
    }

    /**
     * Buku yang sedang dipinjam anggota ini (belum dikembalikan).
     *
     * @return HasManyThrough<DetailPeminjaman, Peminjaman, $this>
     */
    public function detailAktif(): HasManyThrough
    {
        return $this->hasManyThrough(DetailPeminjaman::class, Peminjaman::class)
            ->whereNull('detail_peminjaman.tanggal_kembali');
    }

    public function scopeAktif(Builder $query): void
    {
        $query->where('aktif', true);
    }

    /**
     * Cari berdasarkan nama atau nomor induk.
     */
    public function scopeCari(Builder $query, string $cari): void
    {
        $query->where(fn (Builder $q) => $q->where('nama', 'like', "%$cari%")->orWhere('nomor_induk', 'like', "%$cari%"));
    }

    /**
     * Kelas siswa, atau "Guru" untuk anggota tanpa kelas.
     */
    protected function labelKelas(): Attribute
    {
        return Attribute::get(fn () => $this->kelas ?? 'Guru');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'aktif' => 'boolean',
        ];
    }
}
