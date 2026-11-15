<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BukuImportWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_buku_berhasil_lewat_gui(): void
    {
        Storage::fake('local');

        $user = $this->buatUser();
        $csv = <<<'CSV'
kode_buku,judul,pengarang,penerbit,tahun_terbit,isbn,kategori,jumlah,kondisi
BK-0001,Matematika Kelas 1,Budi,Erlangga,2020,978-1-1,Matematika,3,Baik
BK-0002,Bahasa Indonesia,Ani,,2021,,Bahasa,2,Rusak Ringan
CSV;

        $response = $this->actingAs($user)
            ->post(route('buku.import.store'), ['file' => $this->unggahCsv($csv)]);

        $response->assertRedirect(route('buku.import'));
        $response->assertSessionHas('hasil');
        $this->assertSame(2, session('hasil')['berhasil']);

        $this->assertDatabaseCount('buku', 2);
        $this->assertDatabaseHas('buku', ['kode_buku' => 'BK-0001', 'jumlah' => 3]);
        $this->assertTrue(Kategori::where('nama', 'Matematika')->exists());
    }

    public function test_halaman_import_butuh_login(): void
    {
        $this->get(route('buku.import'))->assertRedirect(route('login'));
    }

    public function test_template_dapat_diunduh(): void
    {
        $this->actingAs($this->buatUser())
            ->get(route('buku.import.template'))
            ->assertOk();
    }

    private function buatUser(): User
    {
        return User::forceCreate([
            'name' => 'Petugas',
            'username' => 'petugas',
            'password' => bcrypt('rahasia'),
        ]);
    }

    private function unggahCsv(string $isi): UploadedFile
    {
        $tmp = tempnam(sys_get_temp_dir(), 'buku_import_');

        file_put_contents($tmp, $isi);

        return new UploadedFile($tmp, 'buku.csv', 'text/csv', null, true);
    }
}
