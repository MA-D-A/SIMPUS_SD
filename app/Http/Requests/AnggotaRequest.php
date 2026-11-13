<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AnggotaRequest extends FormRequest
{
    /**
     * Anggota baru selalu aktif; status hanya bisa diubah di form ubah.
     * Guru tidak punya kelas; kelas siswa disimpan tanpa spasi dan huruf besar (misalnya "4A").
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'aktif' => $this->route('anggota') ? $this->boolean('aktif') : true,
            'kelas' => $this->input('jenis') === 'guru' || blank($this->input('kelas'))
                ? null
                : Str::upper(str_replace(' ', '', $this->input('kelas'))),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nomor_induk' => ['required', 'string', 'max:30', Rule::unique('anggota', 'nomor_induk')->ignore($this->route('anggota'))],
            'nama' => ['required', 'string', 'max:150'],
            'jenis' => ['required', 'in:siswa,guru'],
            'kelas' => ['nullable', 'required_if:jenis,siswa', 'string', 'max:10'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'aktif' => ['boolean'],
        ];
    }
}
