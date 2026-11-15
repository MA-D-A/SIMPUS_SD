<?php

namespace App\Console\Commands;

use App\Services\BukuImporter;
use Illuminate\Console\Command;

class BukuImport extends Command
{
    /**
     * @var string
     */
    protected $signature = 'buku:import
        {file : Path ke file Excel (.xlsx) atau CSV. Baris pertama = header.}';

    /**
     * @var string
     */
    protected $description = 'Import data buku dari Excel/CSV. Kolom: kode_buku, judul, pengarang, penerbit, tahun_terbit, isbn, kategori, jumlah, kondisi.';

    public function handle(BukuImporter $importer): int
    {
        $file = $this->argument('file');

        if (! is_file($file)) {
            $this->error("File tidak ditemukan: $file");

            return self::FAILURE;
        }

        $hasil = $importer->import($file);

        $this->info(sprintf('Selesai: %d berhasil, %d gagal.', $hasil['berhasil'], count($hasil['gagal'])));

        foreach ($hasil['gagal'] as $pesan) {
            $this->warn($pesan);
        }

        return $hasil['berhasil'] > 0 ? self::SUCCESS : self::FAILURE;
    }
}
