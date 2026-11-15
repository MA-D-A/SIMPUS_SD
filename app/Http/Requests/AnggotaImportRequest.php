<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnggotaImportRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'extensions:xlsx,csv', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'file.required' => 'Pilih file Excel (.xlsx) atau CSV.',
            'file.extensions' => 'File harus berformat .xlsx atau .csv.',
            'file.max' => 'Ukuran file maksimal 2 MB.',
        ];
    }
}
