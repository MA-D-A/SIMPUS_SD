<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PengaturanRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nama_sekolah' => ['required', 'string', 'max:150'],
            'alamat_sekolah' => ['nullable', 'string', 'max:255'],
            'nama_kepala_sekolah' => ['nullable', 'string', 'max:150'],
            'nip_kepala_sekolah' => ['nullable', 'string', 'max:30'],
            'lama_pinjam' => ['required', 'integer', 'between:1,60'],
            'maks_pinjam' => ['required', 'integer', 'between:1,10'],
            'nama_petugas' => ['required', 'string', 'max:100'],
        ];
    }
}
