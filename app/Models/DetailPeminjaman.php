<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('detail_peminjaman')]
#[Fillable(['buku_id', 'tanggal_kembali'])]
class DetailPeminjaman extends Model
{
    /** @return BelongsTo<Peminjaman, $this> */
    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class);
    }

    /** @return BelongsTo<Buku, $this> */
    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class);
    }

    public function scopeBelumKembali(Builder $query): void
    {
        $query->whereNull('tanggal_kembali');
    }

    /**
     * BR-06: belum kembali dan jatuh tempo peminjamannya sudah lewat.
     */
    public function scopeTerlambat(Builder $query): void
    {
        $query->belumKembali()->whereHas('peminjaman', fn (Builder $q) => $q->terlambat());
    }

    /**
     * Urutkan menurut jatuh tempo peminjamannya (paling lama di atas).
     */
    public function scopeUrutJatuhTempo(Builder $query): void
    {
        $query->orderBy(Peminjaman::select('tanggal_jatuh_tempo')->whereColumn('peminjaman.id', 'detail_peminjaman.peminjaman_id'))
            ->orderBy('detail_peminjaman.id');
    }

    /**
     * Status per buku: selesai jika sudah kembali, selain itu ikut status peminjamannya.
     */
    public function statusTampilan(): string
    {
        return $this->sudahKembali() ? 'selesai' : $this->peminjaman->statusTampilan();
    }

    public function sudahKembali(): bool
    {
        return $this->tanggal_kembali !== null;
    }

    /**
     * BR-06: (tanggal kembali, atau hari ini jika belum kembali) − jatuh tempo, minimal 0.
     */
    public function hariTerlambat(): int
    {
        return $this->peminjaman->hariTerlambat($this->tanggal_kembali ?? today());
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_kembali' => 'date',
        ];
    }
}
