<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('peminjaman')]
#[Fillable(['anggota_id', 'tanggal_pinjam', 'tanggal_jatuh_tempo', 'status', 'catatan'])]
class Peminjaman extends Model
{
    use HasFactory;

    /** @return BelongsTo<Anggota, $this> */
    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class);
    }

    /** @return HasMany<DetailPeminjaman, $this> */
    public function detail(): HasMany
    {
        return $this->hasMany(DetailPeminjaman::class)->chaperone();
    }

    public function scopeAktif(Builder $query): void
    {
        $query->where('status', 'dipinjam');
    }

    /**
     * BR-06: belum selesai dan jatuh temponya sudah lewat.
     */
    public function scopeTerlambat(Builder $query): void
    {
        $query->aktif()->where('tanggal_jatuh_tempo', '<', today()->toDateString());
    }

    /**
     * Cari berdasarkan nama/nomor induk anggota atau nomor transaksi ("PJ-000123" atau "123").
     */
    public function scopeCari(Builder $query, string $cari): void
    {
        $id = (int) preg_replace('/\D/', '', $cari);

        $query->where(fn (Builder $q) => $q
            ->whereHas('anggota', fn (Builder $a) => $a->cari($cari))
            ->when($id > 0, fn (Builder $q) => $q->orWhere('peminjaman.id', $id)));
    }

    /**
     * Nomor transaksi, misalnya PJ-000123.
     */
    protected function kode(): Attribute
    {
        return Attribute::get(fn () => 'PJ-'.str_pad((string) $this->id, 6, '0', STR_PAD_LEFT));
    }

    public function isTerlambat(): bool
    {
        return $this->status === 'dipinjam' && $this->tanggal_jatuh_tempo->lt(today());
    }

    /**
     * BR-06: ($acuan − jatuh tempo) dalam hari, minimal 0.
     * Dihitung dengan Carbon, bukan SQL, supaya sama di MySQL dan SQLite.
     */
    public function hariTerlambat(CarbonInterface $acuan): int
    {
        return max(0, (int) $this->tanggal_jatuh_tempo->diffInDays($acuan, false));
    }

    /**
     * BR-09: hanya bisa dibatalkan jika belum ada buku yang dikembalikan.
     */
    public function bisaDibatalkan(): bool
    {
        return ! $this->detail()->whereNotNull('tanggal_kembali')->exists();
    }

    /**
     * Status untuk ditampilkan: dipinjam, terlambat, atau selesai.
     */
    public function statusTampilan(): string
    {
        return match (true) {
            $this->status === 'selesai' => 'selesai',
            $this->isTerlambat() => 'terlambat',
            default => 'dipinjam',
        };
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_pinjam' => 'date',
            'tanggal_jatuh_tempo' => 'date',
        ];
    }
}
