<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AnggotaImportWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_anggota_berhasil_lewat_gui(): void
    {
        Storage::fake('local');

        $csv = <<<'CSV'
nomor_induk,nama,jenis,kelas,jenis_kelamin,no_hp
2024001,Budi Santoso,siswa,4 a,laki-laki,081234567891
1999001,Siti Aminah,guru,,perempuan,
CSV;

        $response = $this->actingAs($this->buatUser())
            ->post(route('anggota.import.store'), ['file' => $this->unggahCsv($csv)]);

        $response->assertRedirect(route('anggota.import'));
        $response->assertSessionHas('hasil');
        $this->assertSame(2, session('hasil')['berhasil']);

        $this->assertDatabaseCount('anggota', 2);
        $this->assertDatabaseHas('anggota', [
            'nomor_induk' => '2024001',
            'jenis' => 'siswa',
            'kelas' => '4A',
            'jenis_kelamin' => 'L',
            'no_hp' => '081234567891',
            'aktif' => true,
        ]);
        $this->assertDatabaseHas('anggota', [
            'nomor_induk' => '1999001',
            'jenis' => 'guru',
            'kelas' => null,
            'jenis_kelamin' => 'P',
        ]);
    }

    public function test_baris_gagal_tidak_membatalkan_baris_lain(): void
    {
        Storage::fake('local');

        $csv = <<<'CSV'
nomor_induk,nama,jenis,kelas,jenis_kelamin
,Budi Tanpa Nomor,siswa,4A,L
2024002,Ani,siswa,4B,P
CSV;

        $response = $this->actingAs($this->buatUser())
            ->post(route('anggota.import.store'), ['file' => $this->unggahCsv($csv)]);

        $response->assertRedirect(route('anggota.import'));
        $this->assertSame(1, session('hasil')['berhasil']);
        $this->assertDatabaseCount('anggota', 1);
        $this->assertDatabaseHas('anggota', ['nomor_induk' => '2024002']);
    }

    public function test_nomor_induk_duplikat_ditolak(): void
    {
        Storage::fake('local');

        Anggota::create([
            'nomor_induk' => '2024001',
            'nama' => 'Sudah Ada',
            'jenis' => 'siswa',
            'kelas' => '4A',
            'jenis_kelamin' => 'L',
            'aktif' => true,
        ]);

        $csv = <<<'CSV'
nomor_induk,nama,jenis,kelas,jenis_kelamin
2024001,Duplikat,siswa,4A,L
CSV;

        $this->actingAs($this->buatUser())
            ->post(route('anggota.import.store'), ['file' => $this->unggahCsv($csv)]);

        $this->assertSame(0, session('hasil')['berhasil']);
        $this->assertDatabaseCount('anggota', 1);
    }

    public function test_halaman_import_butuh_login(): void
    {
        $this->get(route('anggota.import'))->assertRedirect(route('login'));
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
        $tmp = tempnam(sys_get_temp_dir(), 'anggota_import_');

        file_put_contents($tmp, $isi);

        return new UploadedFile($tmp, 'anggota.csv', 'text/csv', null, true);
    }
}
