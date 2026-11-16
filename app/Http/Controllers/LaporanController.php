<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\DetailPeminjaman;
use App\Models\Kategori;
use App\Models\Peminjaman;
use Barryvdh\DomPDF\Facade\Pdf;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Symfony\Component\HttpFoundation\Response;

class LaporanController extends Controller
{
    /**
     * dompdf menyimpan seluruh halaman di memori: 1.000 baris laporan peminjaman butuh ±290 MB.
     * Laporan yang lebih besar diarahkan ke filter, Excel, atau versi cetak browser.
     */
    public const BATAS_BARIS_PDF = 1000;

    public function index(): View
    {
        return view('laporan.index');
    }

    /**
     * SKPL-F-014: peminjaman dalam satu periode.
     */
    public function peminjaman(Request $request): View|Response|RedirectResponse
    {
        $request->mergeIfMissing(['dari' => today()->startOfMonth()->toDateString(), 'sampai' => today()->toDateString()]);
        $filter = $request->validate([
            'dari' => ['required', 'date'],
            'sampai' => ['required', 'date', 'after_or_equal:dari'],
        ], ['sampai.after_or_equal' => 'Tanggal awal harus sebelum tanggal akhir.']);
        $dari = Carbon::parse($filter['dari']);
        $sampai = Carbon::parse($filter['sampai']);

        $query = DetailPeminjaman::with(['buku', 'peminjaman.anggota'])
            ->whereHas('peminjaman', fn ($q) => $q->whereDate('tanggal_pinjam', '>=', $dari)->whereDate('tanggal_pinjam', '<=', $sampai))
            ->orderBy(Peminjaman::select('tanggal_pinjam')->whereColumn('peminjaman.id', 'detail_peminjaman.peminjaman_id'))
            ->orderBy('detail_peminjaman.peminjaman_id')
            ->orderBy('detail_peminjaman.id');

        return $this->keluaran($request, 'peminjaman', $query, fn (Collection $baris) => [
            'Transaksi' => $baris->unique('peminjaman_id')->count(),
            'Buku dipinjam' => $baris->count(),
            'Sudah kembali' => $baris->filter->sudahKembali()->count(),
            'Belum kembali' => $baris->reject->sudahKembali()->count(),
            'Terlambat' => $baris->filter(fn ($d) => $d->statusTampilan() === 'terlambat')->count(),
        ], [
            'judul' => 'Laporan Peminjaman Buku',
            'keterangan' => 'Periode '.$dari->translatedFormat('j F Y').' s.d. '.$sampai->translatedFormat('j F Y'),
            'namaFile' => "laporan-peminjaman-{$dari->toDateString()}_{$sampai->toDateString()}",
            'filter' => $filter,
        ], ['No', 'No. transaksi', 'Tanggal pinjam', 'NIS', 'Nama', 'Kelas', 'Kode buku', 'Judul', 'Jatuh tempo', 'Tanggal kembali', 'Status', 'Hari terlambat'],
            fn (DetailPeminjaman $d, int $no) => [
                $no, $d->peminjaman->kode, $d->peminjaman->tanggal_pinjam->format('d/m/Y'),
                $d->peminjaman->anggota->nomor_induk, $d->peminjaman->anggota->nama, $d->peminjaman->anggota->label_kelas,
                $d->buku->kode_buku, $d->buku->judul, $d->peminjaman->tanggal_jatuh_tempo->format('d/m/Y'),
                $d->tanggal_kembali?->format('d/m/Y') ?? '', ucfirst($d->statusTampilan()), $d->hariTerlambat(),
            ]);
    }

    /**
     * SKPL-F-015: buku yang belum dikembalikan (posisi hari ini).
     */
    public function belumKembali(Request $request): View|Response|RedirectResponse
    {
        $hanyaTerlambat = $request->boolean('terlambat');

        $query = DetailPeminjaman::query()
            ->when($hanyaTerlambat, fn ($q) => $q->terlambat(), fn ($q) => $q->belumKembali())
            ->with(['buku', 'peminjaman.anggota'])
            ->urutJatuhTempo();

        return $this->keluaran($request, 'belum-kembali', $query, fn (Collection $baris) => [
            'Buku belum kembali' => $baris->count(),
            'Terlambat' => $baris->filter(fn ($d) => $d->hariTerlambat() > 0)->count(),
        ], [
            'judul' => $hanyaTerlambat ? 'Laporan Buku Terlambat' : 'Laporan Buku Belum Kembali',
            'keterangan' => 'Per '.today()->translatedFormat('j F Y'),
            'namaFile' => ($hanyaTerlambat ? 'laporan-terlambat-' : 'laporan-belum-kembali-').today()->toDateString(),
            'hanyaTerlambat' => $hanyaTerlambat,
        ], ['No', 'No. transaksi', 'NIS', 'Nama', 'Kelas', 'Kode buku', 'Judul', 'Tanggal pinjam', 'Jatuh tempo', 'Hari terlambat'],
            fn (DetailPeminjaman $d, int $no) => [
                $no, $d->peminjaman->kode, $d->peminjaman->anggota->nomor_induk, $d->peminjaman->anggota->nama,
                $d->peminjaman->anggota->label_kelas, $d->buku->kode_buku, $d->buku->judul,
                $d->peminjaman->tanggal_pinjam->format('d/m/Y'), $d->peminjaman->tanggal_jatuh_tempo->format('d/m/Y'), $d->hariTerlambat(),
            ]);
    }

    /**
     * SKPL-F-016: inventaris buku beserta stoknya.
     */
    public function buku(Request $request): View|Response|RedirectResponse
    {
        $kategori = Kategori::find($request->integer('kategori'));
        $kondisi = $request->validate([
            'kondisi' => ['nullable', Rule::in(['Baik', 'Rusak Ringan', 'Rusak Berat'])],
        ])['kondisi'] ?? null;

        $query = Buku::denganStok()
            ->with('kategori')
            ->when($kategori, fn ($q) => $q->whereBelongsTo($kategori))
            ->when($kondisi, fn ($q) => $q->where('kondisi', $kondisi))
            ->orderBy(Kategori::select('nama')->whereColumn('kategori.id', 'buku.kategori_id'))
            ->orderBy('judul')
            ->orderBy('buku.id');

        return $this->keluaran($request, 'buku', $query, fn (Collection $baris) => [
            'Judul' => $baris->count(),
            'Eksemplar' => $baris->sum('jumlah'),
            'Dipinjam' => $baris->sum('dipinjam_count'),
            'Tersedia' => $baris->sum('tersedia'),
        ], [
            'judul' => 'Laporan Inventaris Buku',
            'keterangan' => ($kategori ? "Kategori {$kategori->nama} · " : 'Semua kategori · ')
                .($kondisi ? "Kondisi {$kondisi} · " : '')
                .'Per '.today()->translatedFormat('j F Y'),
            'namaFile' => 'laporan-inventaris-buku-'.today()->toDateString(),
            'semuaKategori' => Kategori::orderBy('nama')->get(),
        ], ['No', 'Kode', 'Judul', 'Pengarang', 'Penerbit', 'Tahun', 'Kategori', 'Kondisi', 'Jumlah', 'Dipinjam', 'Tersedia'],
            fn (Buku $b, int $no) => [
                $no, $b->kode_buku, $b->judul, $b->pengarang, $b->penerbit ?? '', $b->tahun_terbit ?? '',
                $b->kategori->nama, $b->kondisi, $b->jumlah, $b->dipinjam_count, $b->tersedia,
            ]);
    }

    /**
     * SKPL-F-017: tampilkan di layar, versi cetak, PDF, atau Excel sesuai ?format=.
     *
     * @param  Closure(Collection<int, mixed>): array<string, int>  $rekap
     * @param  array<string, mixed>  $data  judul, keterangan, namaFile, dan data filter
     * @param  array<int, string>  $kolomExcel
     */
    private function keluaran(Request $request, string $jenis, Builder $query, Closure $rekap, array $data, array $kolomExcel, Closure $barisExcel): View|Response|RedirectResponse
    {
        $format = $request->query('format');

        // Excel ditulis bertahap per 1.000 baris supaya laporan puluhan ribu baris tidak menghabiskan memori.
        if ($format === 'excel') {
            return $this->excel($query->lazy(), $data['namaFile'], $kolomExcel, $barisExcel);
        }

        if ($format === 'pdf' && ($jumlah = $query->count()) > self::BATAS_BARIS_PDF) {
            return redirect($request->fullUrlWithoutQuery('format'))->with('gagal', 'Laporan ini berisi '.number_format($jumlah, 0, ',', '.')
                .' baris, sedangkan PDF dibatasi '.number_format(self::BATAS_BARIS_PDF, 0, ',', '.').' baris agar komputer tidak kehabisan memori.'
                .' Persempit filternya, atau gunakan Excel.');
        }

        $baris = $query->get();
        $data += ['jenis' => $jenis, 'baris' => $baris, 'rekap' => $rekap($baris)];

        return match ($format) {
            'cetak' => view('laporan.cetak', $data),
            'pdf' => $this->pdf($data),
            default => view("laporan.$jenis", $data + ['halaman' => $this->halaman($request, $baris)]),
        };
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function pdf(array $data): Response
    {
        // Cukup untuk BATAS_BARIS_PDF; batas bawaan PHP (128 MB) hanya muat beberapa ratus baris.
        ini_set('memory_limit', '384M');

        // Subsetting: hanya huruf yang dipakai yang disematkan (berkas puluhan KB, bukan ±900 KB).
        return Pdf::loadView('laporan.cetak', $data + ['pdf' => true])
            ->setOption('isFontSubsettingEnabled', true)
            ->setPaper('a4', 'landscape')
            ->download($data['namaFile'].'.pdf');
    }

    /**
     * @param  Collection<int, mixed>  $baris
     * @return LengthAwarePaginator<int, mixed>
     */
    private function halaman(Request $request, Collection $baris): LengthAwarePaginator
    {
        $nomor = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator($baris->forPage($nomor, 25)->values(), $baris->count(), 25, $nomor, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);
    }

    /**
     * @param  iterable<int, mixed>  $baris
     * @param  array<int, string>  $kolom
     */
    private function excel(iterable $baris, string $namaFile, array $kolom, Closure $isiBaris): Response
    {
        $path = storage_path('app/'.Str::uuid().'.xlsx');
        $writer = SimpleExcelWriter::create($path)->addHeader($kolom);

        foreach ($baris as $i => $item) {
            $writer->addRow($isiBaris($item, $i + 1));
        }

        $writer->close();

        return response()->download($path, "$namaFile.xlsx")->deleteFileAfterSend();
    }
}
