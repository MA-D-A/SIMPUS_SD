<?php

namespace App\Http\Controllers;

use App\Http\Requests\BukuImportRequest;
use App\Http\Requests\BukuRequest;
use App\Models\Buku;
use App\Models\Kategori;
use App\Services\BukuImporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\SimpleExcel\SimpleExcelWriter;

class BukuController extends Controller
{
    public function index(Request $request): View
    {
        $buku = Buku::query()
            ->with('kategori')
            ->denganStok()
            ->when($request->string('cari')->trim()->value(), fn ($query, $cari) => $query->cari($cari))
            ->when($request->integer('kategori'), fn ($query, $id) => $query->where('kategori_id', $id))
            ->orderBy('judul')
            ->paginate(15)
            ->withQueryString();

        return view('buku.index', ['buku' => $buku, 'semuaKategori' => Kategori::orderBy('nama')->get()]);
    }

    public function create(): View
    {
        return view('buku.form', [
            'buku' => new Buku(['kode_buku' => Buku::kodeBerikutnya(), 'jumlah' => 1, 'kondisi' => 'Baik']),
            'semuaKategori' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function importForm(): View
    {
        return view('buku.import');
    }

    public function importStore(BukuImportRequest $request, BukuImporter $importer): RedirectResponse
    {
        $berkas = $request->file('file');
        $ext = strtolower($berkas->getClientOriginalExtension());
        $path = $berkas->storeAs('import', Str::uuid().'.'.$ext);

        $hasil = $importer->import(Storage::path($path));

        Storage::delete($path);

        return redirect()
            ->route('buku.import')
            ->with('hasil', $hasil);
    }

    public function importTemplate()
    {
        $path = storage_path('app/'.Str::uuid().'.xlsx');

        $writer = SimpleExcelWriter::create($path)
            ->addHeader(['kode_buku', 'judul', 'pengarang', 'penerbit', 'tahun_terbit', 'isbn', 'kategori', 'jumlah', 'kondisi']);
        $writer->close();

        return response()->download($path, 'template-buku.xlsx')->deleteFileAfterSend();
    }

    public function store(BukuRequest $request): RedirectResponse
    {
        Buku::create($request->validated());

        return redirect()->route('buku.index')->with('sukses', 'Buku berhasil ditambahkan.');
    }

    public function show(Buku $buku): View
    {
        $buku->load('kategori');
        $riwayat = $buku->detailPeminjaman()->with('peminjaman.anggota')->latest('id')->take(10)->get();

        return view('buku.show', compact('buku', 'riwayat'));
    }

    public function edit(Buku $buku): View
    {
        return view('buku.form', ['buku' => $buku, 'semuaKategori' => Kategori::orderBy('nama')->get()]);
    }

    public function update(BukuRequest $request, Buku $buku): RedirectResponse
    {
        $buku->update($request->validated());

        return redirect()->route('buku.show', $buku)->with('sukses', 'Data buku berhasil diubah.');
    }

    public function destroy(Buku $buku): RedirectResponse
    {
        // BR-07: buku yang sudah punya riwayat peminjaman tidak bisa dihapus.
        if ($buku->detailPeminjaman()->exists()) {
            return back()->with('gagal', 'Buku tidak bisa dihapus karena sudah punya riwayat peminjaman. Ubah jumlahnya menjadi 0 jika buku sudah tidak ada.');
        }

        $buku->delete();

        return redirect()->route('buku.index')->with('sukses', 'Buku berhasil dihapus.');
    }
}
