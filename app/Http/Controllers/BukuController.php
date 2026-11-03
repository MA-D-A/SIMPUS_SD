<?php

namespace App\Http\Controllers;

use App\Http\Requests\BukuRequest;
use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
            'buku' => new Buku(['kode_buku' => Buku::kodeBerikutnya(), 'jumlah' => 1]),
            'semuaKategori' => Kategori::orderBy('nama')->get(),
        ]);
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
