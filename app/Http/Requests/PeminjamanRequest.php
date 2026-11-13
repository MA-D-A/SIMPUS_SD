<?php

namespace App\Http\Requests;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Pengaturan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

class PeminjamanRequest extends FormRequest
{
    /**
     * Saran di form berbentuk "2024001 - Andi Saputra (4A)" dan "BK-0012 - Judul";
     * yang dipakai hanya bagian sebelum " - ". Baris buku yang kosong dibuang.
     */
    protected function prepareForValidation(): void
    {
        $kode = fn ($nilai) => trim(Str::before((string) $nilai, ' - '));

        $this->merge([
            'nomor_induk' => $kode($this->input('nomor_induk')),
            'kode_buku' => collect($this->input('kode_buku', []))->map($kode)->filter()->values()->all(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nomor_induk' => ['required', 'exists:anggota,nomor_induk'],
            'kode_buku' => ['required', 'array', 'min:1'],
            'kode_buku.*' => ['string', 'distinct', 'exists:buku,kode_buku'],
            'tanggal_pinjam' => ['required', 'date', 'before_or_equal:today'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nomor_induk.required' => 'Isi anggota yang meminjam.',
            'nomor_induk.exists' => 'Anggota tidak ditemukan.',
            'kode_buku.required' => 'Isi minimal satu buku.',
            'kode_buku.*.exists' => 'Buku dengan kode :input tidak ditemukan.',
            'kode_buku.*.distinct' => 'Buku :input diisi lebih dari satu kali.',
            'tanggal_pinjam.before_or_equal' => 'Tanggal pinjam tidak boleh setelah hari ini.',
        ];
    }

    /**
     * Aturan bisnis BR-01 s.d. BR-04, diperiksa setelah aturan dasar lolos.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $anggota = $this->anggota();

            if (! $anggota->aktif) {
                $validator->errors()->add('nomor_induk', 'Anggota tidak aktif.');

                return;
            }

            $sedangDipinjam = $anggota->detailAktif()->with(['buku', 'peminjaman'])->get();
            $terlambat = $sedangDipinjam->first(fn ($detail) => $detail->peminjaman->isTerlambat());

            if ($terlambat) {
                $jatuhTempo = $terlambat->peminjaman->tanggal_jatuh_tempo->translatedFormat('j F Y');
                $validator->errors()->add('nomor_induk', "Anggota masih punya pinjaman terlambat: {$terlambat->buku->judul} (jatuh tempo $jatuhTempo).");

                return;
            }

            $maks = Pengaturan::ambil()->maks_pinjam;

            if ($sedangDipinjam->count() + $this->buku()->count() > $maks) {
                $validator->errors()->add('kode_buku', "Anggota sudah meminjam {$sedangDipinjam->count()} buku; maksimal $maks buku.");
            }

            foreach ($this->buku() as $buku) {
                if ($sedangDipinjam->contains('buku_id', $buku->id)) {
                    $validator->errors()->add('kode_buku', "{$buku->judul} masih dipinjam anggota ini.");
                } elseif ($buku->tersedia <= 0) {
                    $validator->errors()->add('kode_buku', "Stok {$buku->judul} sedang habis.");
                }
            }
        }];
    }

    public function anggota(): Anggota
    {
        return once(fn () => Anggota::where('nomor_induk', $this->input('nomor_induk'))->firstOrFail());
    }

    /**
     * @return Collection<int, Buku>
     */
    public function buku(): Collection
    {
        return once(fn () => Buku::denganStok()->whereIn('kode_buku', $this->input('kode_buku'))->get());
    }
}
