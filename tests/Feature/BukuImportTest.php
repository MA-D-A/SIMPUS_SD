<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BukuImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_buku_dari_csv_lengkap(): void
    {
        $file = $this->buatCsv(<<<'CSV'
kode_buku,judul,pengarang,penerbit,tahun_terbit,isbn,kategori,jumlah,kondisi
BK-0001,Matematika Kelas 1,Budi,Erlangga,2020,978-1-1,Matematika,3,Baik
BK-0002,Bahasa Indonesia,Ani,,2021,,Bahasa,2,Rusak Ringan
CSV);

        $this->artisan('buku:import', ['file' => $file])
            ->assertSuccessful()
            ->expectsOutputToContain('2 berhasil, 0 gagal');

        $this->assertDatabaseCount('buku', 2);
        $this->assertDatabaseCount('kategori', 2);
        $this->assertDatabaseHas('buku', ['kode_buku' => 'BK-0001', 'jumlah' => 3, 'kondisi' => 'Baik']);
        $this->assertTrue(Kategori::where('nama', 'Matematika')->exists());
    }

    public function test_baris_gagal_tidak_membatalkan_baris_lain(): void
    {
        $file = $this->buatCsv(<<<'CSV'
kode_buku,judul,pengarang,kategori
BK-0001,Buku A,Penulis,Sains
BK-0001,Buku B,Penulis,Sains
BK-0002,Buku C,Penulis,Sains
CSV);

        $this->artisan('buku:import', ['file' => $file])
            ->assertSuccessful()
            ->expectsOutputToContain('2 berhasil, 1 gagal');

        $this->assertDatabaseCount('buku', 2);
        $this->assertDatabaseHas('buku', ['kode_buku' => 'BK-0002']);
    }

    public function test_kategori_auto_dibuat_dan_default_jumlah_kondisi(): void
    {
        $file = $this->buatCsv(<<<'CSV'
kode_buku,judul,pengarang,kategori
BK-0001,Buku X,Penulis,Fiksi
CSV);

        $this->artisan('buku:import', ['file' => $file])->assertSuccessful();

        $buku = Buku::where('kode_buku', 'BK-0001')->first();

        $this->assertSame('Baik', $buku->kondisi);
        $this->assertSame(1, $buku->jumlah);
        $this->assertSame('Fiksi', $buku->kategori->nama);
    }

    public function test_kode_buku_kosong_diisi_otomatis(): void
    {
        $file = $this->buatCsv(<<<'CSV'
kode_buku,judul,pengarang,kategori
,Buku Tanpa Kode,Penulis,Sains
CSV);

        $this->artisan('buku:import', ['file' => $file])->assertSuccessful();

        $this->assertDatabaseHas('buku', ['kode_buku' => 'BK-0001', 'judul' => 'Buku Tanpa Kode']);
    }

    public function test_kode_otomatis_melompati_kode_yang_sudah_ada(): void
    {
        $kategori = Kategori::create(['nama' => 'Sains']);
        Buku::create([
            'kode_buku' => 'BK-0002',
            'judul' => 'Buku Lama',
            'pengarang' => 'Penulis',
            'kategori_id' => $kategori->id,
            'jumlah' => 1,
            'kondisi' => 'Baik',
        ]);

        $file = $this->buatCsv(<<<'CSV'
kode_buku,judul,pengarang,kategori
,Buku Baru,Penulis,Sains
CSV);

        $this->artisan('buku:import', ['file' => $file])->assertSuccessful();

        $this->assertDatabaseHas('buku', ['kode_buku' => 'BK-0003', 'judul' => 'Buku Baru']);
    }

    public function test_file_tidak_ada_gagal(): void
    {
        $this->artisan('buku:import', ['file' => '/tidak/ada/file.xlsx'])
            ->assertFailed()
            ->expectsOutputToContain('File tidak ditemukan');
    }

    private function buatCsv(string $isi): string
    {
        $file = tempnam(sys_get_temp_dir(), 'buku_import_');
        $fileCsv = $file.'.csv';
        rename($file, $fileCsv);
        file_put_contents($fileCsv, $isi);

        return $fileCsv;
    }
}
