<?php

namespace App\Http\Controllers;

use App\Http\Requests\KategoriRequest;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KategoriController extends Controller
{
    public function index(): View
    {
        $kategori = Kategori::withCount('buku')->orderBy('nama')->paginate(15);

        return view('kategori.index', compact('kategori'));
    }

    public function create(): View
    {
        return view('kategori.form', ['kategori' => new Kategori]);
    }

    public function store(KategoriRequest $request): RedirectResponse
    {
        Kategori::create($request->validated());

        return redirect()->route('kategori.index')->with('sukses', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Kategori $kategori): View
    {
        return view('kategori.form', compact('kategori'));
    }

    public function update(KategoriRequest $request, Kategori $kategori): RedirectResponse
    {
        $kategori->update($request->validated());

        return redirect()->route('kategori.index')->with('sukses', 'Kategori berhasil diubah.');
    }

    public function destroy(Kategori $kategori): RedirectResponse
    {
        // BR-07: kategori yang masih dipakai buku tidak bisa dihapus.
        if ($jumlah = $kategori->buku()->count()) {
            return back()->with('gagal', "Kategori tidak bisa dihapus karena masih dipakai oleh $jumlah buku.");
        }

        $kategori->delete();

        return redirect()->route('kategori.index')->with('sukses', 'Kategori berhasil dihapus.');
    }
}
