<?php

namespace App\Http\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BukuRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $buku = $this->route('buku');

        return [
            'kode_buku' => ['required', 'string', 'max:20', Rule::unique('buku', 'kode_buku')->ignore($buku)],
            'judul' => ['required', 'string', 'max:255'],
            'pengarang' => ['required', 'string', 'max:150'],
            'penerbit' => ['nullable', 'string', 'max:150'],
            'tahun_terbit' => ['nullable', 'integer', 'between:1901,'.now()->year],
            'isbn' => ['nullable', 'string', 'max:20'],
            'kategori_id' => ['required', 'exists:kategori,id'],
            'jumlah' => [
                'required', 'integer', 'min:0', 'max:9999',
                // BR-08: tidak boleh kurang dari eksemplar yang sedang dipinjam.
                function (string $attribute, mixed $value, Closure $fail) use ($buku) {
                    if ($buku && (int) $value < $dipinjam = $buku->jumlahDipinjam()) {
                        $fail("Jumlah tidak boleh kurang dari $dipinjam eksemplar yang sedang dipinjam.");
                    }
                },
            ],
            'kondisi' => ['required', 'string', Rule::in(['Baik', 'Rusak Ringan', 'Rusak Berat'])],
        ];
    }
}