<?php

namespace App\Services;

use App\Models\Anggota;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\SimpleExcel\SimpleExcelReader;

class AnggotaImporter
{
    /**
     * Header file dipetakan ke nama kolom database tanpa peduli spasi/huruf besar.
     */
    private const HEADER = [
        'nomorinduk' => 'nomor_induk',
        'nis' => 'nomor_induk',
        'nip' => 'nomor_induk',
        'nama' => 'nama',
        'jenis' => 'jenis',
        'kelas' => 'kelas',
        'jeniskelamin' => 'jenis_kelamin',
        'jk' => 'jenis_kelamin',
        'nohp' => 'no_hp',
        'hp' => 'no_hp',
        'telepon' => 'no_hp',
    ];

    /**
     * Import anggota dari file Excel/CSV. Satu baris jelek tidak menggagalkan baris lain.
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
        $nomorDiproses = [];

        foreach ($baris as $index => $row) {
            $nomor = $index + 2; // baris pertama = header

            $row = $this->bersihkan($row);

            if (empty(array_filter($row, fn ($v) => $v !== null))) {
                continue; // baris kosong
            }

            $jenis = isset($row['jenis']) ? strtolower((string) $row['jenis']) : null;

            $data = [
                'nomor_induk' => $row['nomor_induk'] ?? null,
                'nama' => $row['nama'] ?? null,
                'jenis' => $jenis,
                'kelas' => $this->normalisasiKelas($row['kelas'] ?? null, $jenis),
                'jenis_kelamin' => $this->normalisasiJenisKelamin($row['jenis_kelamin'] ?? null),
                'no_hp' => $row['no_hp'] ?? null,
                'aktif' => true,
            ];

            $kesalahan = $this->validasi($data, $nomorDiproses);

            if ($kesalahan !== []) {
                $gagal[] = sprintf('Baris %d: %s', $nomor, implode('; ', $kesalahan));

                continue;
            }

            Anggota::create($data);
            $nomorDiproses[] = $data['nomor_induk'];
            $berhasil++;
        }

        return ['berhasil' => $berhasil, 'gagal' => $gagal];
    }

    /**
     * Excel membaca sel numerik sebagai int/float (mis. 8.1234567891E10). Ubah ke string utuh.
     *
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    private function bersihkan(array $row): array
    {
        $hasil = [];

        foreach ($row as $kunci => $nilai) {
            if (is_string($nilai)) {
                $nilai = trim($nilai);
            } elseif (is_float($nilai) && floor($nilai) === $nilai) {
                $nilai = (string) (int) $nilai;
            } elseif (is_int($nilai)) {
                $nilai = (string) $nilai;
            }

            $hasil[$kunci] = $nilai === '' ? null : $nilai;
        }

        return $hasil;
    }

    private function normalizeHeader(string $header): string
    {
        $kunci = strtolower(preg_replace('/[^a-z0-9]/i', '', $header));

        return self::HEADER[$kunci] ?? $kunci;
    }

    private function normalisasiKelas(mixed $kelas, ?string $jenis): ?string
    {
        if ($jenis === 'guru' || blank($kelas)) {
            return null;
        }

        return Str::upper(str_replace(' ', '', (string) $kelas));
    }

    private function normalisasiJenisKelamin(mixed $nilai): ?string
    {
        if ($nilai === null) {
            return null;
        }

        $kunci = strtolower(preg_replace('/[^a-z]/i', '', (string) $nilai));

        return match ($kunci) {
            'l', 'laki', 'lakilaki' => 'L',
            'p', 'perempuan' => 'P',
            default => strtoupper((string) $nilai),
        };
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<string|null>  $nomorDiproses
     * @return list<string>
     */
    private function validasi(array $data, array $nomorDiproses): array
    {
        $kesalahan = [];

        if (in_array($data['nomor_induk'], $nomorDiproses, true)) {
            $kesalahan[] = 'nomor_induk duplikat di dalam file';
        }

        $validator = Validator::make($data, [
            'nomor_induk' => ['required', 'string', 'max:30', 'unique:anggota,nomor_induk'],
            'nama' => ['required', 'string', 'max:150'],
            'jenis' => ['required', 'in:siswa,guru'],
            'kelas' => ['nullable', 'required_if:jenis,siswa', 'string', 'max:10'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'no_hp' => ['nullable', 'string', 'max:20'],
        ]);

        foreach ($validator->errors()->all() as $pesan) {
            $kesalahan[] = $pesan;
        }

        return $kesalahan;
    }
}
