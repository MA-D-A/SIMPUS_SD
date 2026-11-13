<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PengembalianRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $peminjaman = $this->route('peminjaman');

        return [
            'detail_id' => ['required', 'array', 'min:1'],
            'detail_id.*' => ['integer', Rule::in($peminjaman->detail()->whereNull('tanggal_kembali')->pluck('id')->all())],
            'tanggal_kembali' => ['required', 'date', 'after_or_equal:'.$peminjaman->tanggal_pinjam->toDateString(), 'before_or_equal:today'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'detail_id.required' => 'Pilih minimal satu buku yang dikembalikan.',
            'detail_id.*.in' => 'Buku yang dipilih bukan bagian dari peminjaman ini atau sudah dikembalikan.',
            'tanggal_kembali.after_or_equal' => 'Tanggal kembali tidak boleh sebelum tanggal pinjam.',
            'tanggal_kembali.before_or_equal' => 'Tanggal kembali tidak boleh setelah hari ini.',
        ];
    }
}
