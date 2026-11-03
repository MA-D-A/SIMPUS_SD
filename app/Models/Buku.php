<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('buku')]
#[Fillable(['kode_buku', 'judul', 'pengarang', 'penerbit', 'tahun_terbit', 'isbn', 'kategori_id', 'jumlah'])]
class Buku extends Model
{
    use HasFactory;

    /** @return BelongsTo<Kategori, $this> */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    /** @return HasMany<DetailPeminjaman, $this> */
    public function detailPeminjaman(): HasMany
    {
        return $this->hasMany(DetailPeminjaman::class);
    }

    /**
     * Tambahkan kolom dipinjam_count (eksemplar yang belum kembali) agar daftar buku cukup satu query.
     */
    public function scopeDenganStok(Builder $query): void
    {
        $query->withCount(['detailPeminjaman as dipinjam_count' => fn (Builder $q) => $q->whereNull('tanggal_kembali')]);
    }

    /**
     * Cari berdasarkan kode, judul, atau pengarang.
     */
    public function scopeCari(Builder $query, string $cari): void
    {
        $query->where(fn (Builder $q) => $q
            ->where('kode_buku', 'like', "%$cari%")
            ->orWhere('judul', 'like', "%$cari%")
            ->orWhere('pengarang', 'like', "%$cari%"));
    }

    public function jumlahDipinjam(): int
    {
        return $this->dipinjam_count ?? $this->detailPeminjaman()->whereNull('tanggal_kembali')->count();
    }

    /**
     * Stok tersedia = jumlah eksemplar − eksemplar yang sedang dipinjam.
     */
    protected function tersedia(): Attribute
    {
        return Attribute::get(fn () => $this->jumlah - $this->jumlahDipinjam());
    }

    /**
     * Saran kode untuk buku baru, misalnya BK-0001. Admin boleh menggantinya.
     */
    public static function kodeBerikutnya(): string
    {
        return 'BK-'.str_pad((string) ((static::max('id') ?? 0) + 1), 4, '0', STR_PAD_LEFT);
    }
}
