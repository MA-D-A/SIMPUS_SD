<?php

namespace App\Services;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\SimpleExcel\SimpleExcelReader;

class BukuImporter
{
    /**
     * Header file dipetakan ke nama kolom database tanpa peduli spasi/huruf besar.
     */
    private const HEADER = [
        'kodebuku' => 'kode_buku',
        'tahunterbit' => 'tahun_terbit',
        'judul' => 'judul',
        'pengarang' => 'pengarang',
        'penerbit' => 'penerbit',
        'isbn' => 'isbn',
        'kategori' => 'kategori',
        'jumlah' => 'jumlah',
        'kondisi' => 'kondisi',
    ];

    /**
     * Import buku dari file Excel/CSV. Satu baris jelek tidak menggagalkan baris lain.
     *
     * @return array{berhasil: int, gagal: list<string>}
     */
    public function import(string $file): array
    {
        $baris = SimpleExcelReader::create($file)
            ->trimHeaderRow()
            ->formatHeadersUsing(fn (string $header) => $this->normalizeHeader($header))
            ->trimValues()
            ->getRows();

        $berhasil = 0;
        $gagal = [];
        $kodeDiproses = [];
        $judulDiproses = [];

        foreach ($baris as $index => $row) {
            $nomor = $index + 2; // baris pertama = header

            $row = $this->bersihkan($row);

            if (empty(array_filter($row, fn ($v) => $v !== null))) {
                continue; // baris kosong
            }

            $data = [
                'kode_buku' => ($row['kode_buku'] ?? null) ?: $this->kodeBebas(),
                'judul' => $row['judul'] ?? null,
                'pengarang' => $row['pengarang'] ?? null,
                'penerbit' => $row['penerbit'] ?? null,
                'tahun_terbit' => ($row['tahun_terbit'] ?? null) !== null ? (int) $row['tahun_terbit'] : null,
                'isbn' => $row['isbn'] ?? null,
                'kategori_id' => $this->resolusiKategori($row['kategori'] ?? null),
                'jumlah' => isset($row['jumlah']) ? (int) $row['jumlah'] : 1,
                'kondisi' => $row['kondisi'] ?? 'Baik',
            ];

            $kesalahan = $this->validasi($data, $kodeDiproses, $judulDiproses);

            if ($kesalahan !== []) {
                $gagal[] = sprintf('Baris %d: %s', $nomor, implode('; ', $kesalahan));

                continue;
            }

            Buku::create($data);
            $kodeDiproses[] = $data['kode_buku'];
            $judulDiproses[] = $data['judul'];
            $berhasil++;
        }

        return ['berhasil' => $berhasil, 'gagal' => $gagal];
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    private function bersihkan(array $row): array
    {
        $hasil = [];

        foreach ($row as $kunci => $nilai) {
            $nilai = is_string($nilai) ? trim($nilai) : $nilai;
            $hasil[$kunci] = $nilai === '' ? null : $nilai;
        }

        return $hasil;
    }

    private function normalizeHeader(string $header): string
    {
        $kunci = strtolower(preg_replace('/[^a-z0-9]/i', '', $header));

        return self::HEADER[$kunci] ?? $kunci;
    }

    private function kodeBebas(): string
    {
        $kode = Buku::kodeBerikutnya();

        while (Buku::where('kode_buku', $kode)->exists()) {
            $nomor = (int) substr($kode, 3) + 1;
            $kode = 'BK-'.str_pad((string) $nomor, 4, '0', STR_PAD_LEFT);
        }

        return $kode;
    }

    private function resolusiKategori(mixed $nama): ?int
    {
        if ($nama === null) {
            return null;
        }

        return Kategori::firstOrCreate(['nama' => $nama])->id;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<string|null>  $kodeDiproses
     * @param  list<string|null>  $judulDiproses
     * @return list<string>
     */
    private function validasi(array $data, array $kodeDiproses, array $judulDiproses): array
    {
        $kesalahan = [];

        if (in_array($data['kode_buku'], $kodeDiproses, true)) {
            $kesalahan[] = 'kode_buku duplikat di dalam file';
        }

        if (in_array($data['judul'], $judulDiproses, true)) {
            $kesalahan[] = 'judul duplikat di dalam file';
        }

        $validator = Validator::make($data, [
            'kode_buku' => ['required', 'string', 'max:20', 'unique:buku,kode_buku'],
            'judul' => ['required', 'string', 'max:255', 'unique:buku,judul'],
            'pengarang' => ['required', 'string', 'max:150'],
            'penerbit' => ['nullable', 'string', 'max:150'],
            'tahun_terbit' => ['nullable', 'integer', 'between:1901,'.now()->year],
            'isbn' => ['nullable', 'string', 'max:20'],
            'kategori_id' => ['required', 'exists:kategori,id'],
            'jumlah' => ['required', 'integer', 'min:0', 'max:9999'],
            'kondisi' => ['required', 'string', Rule::in(['Baik', 'Rusak Ringan', 'Rusak Berat'])],
        ]);

        foreach ($validator->errors()->all() as $pesan) {
            $kesalahan[] = $pesan;
        }

        return $kesalahan;
    }
}
