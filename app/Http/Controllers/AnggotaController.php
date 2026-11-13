<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnggotaRequest;
use App\Models\Anggota;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnggotaController extends Controller
{
    public function index(Request $request): View
    {
        $anggota = Anggota::query()
            ->when($request->string('cari')->trim()->value(), fn ($query, $cari) => $query->cari($cari))
            ->when($request->input('jenis'), fn ($query, $jenis) => $query->where('jenis', $jenis))
            ->when($request->input('kelas'), fn ($query, $kelas) => $query->where('kelas', $kelas))
            ->when($request->input('status'), fn ($query, $status) => $query->where('aktif', $status === 'aktif'))
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        $semuaKelas = Anggota::whereNotNull('kelas')->distinct()->orderBy('kelas')->pluck('kelas');

        return view('anggota.index', compact('anggota', 'semuaKelas'));
    }

    public function create(): View
    {
        return view('anggota.form', ['anggota' => new Anggota(['jenis' => 'siswa', 'aktif' => true])]);
    }

    public function store(AnggotaRequest $request): RedirectResponse
    {
        Anggota::create($request->validated());

        return redirect()->route('anggota.index')->with('sukses', 'Anggota berhasil ditambahkan.');
    }

    public function show(Anggota $anggota): View
    {
        $sedangDipinjam = $anggota->detailAktif()->with(['buku', 'peminjaman'])->get();
        $riwayat = $anggota->peminjaman()->withCount('detail')->latest('tanggal_pinjam')->latest('id')->take(10)->get();

        return view('anggota.show', compact('anggota', 'sedangDipinjam', 'riwayat'));
    }

    public function edit(Anggota $anggota): View
    {
        return view('anggota.form', compact('anggota'));
    }

    public function update(AnggotaRequest $request, Anggota $anggota): RedirectResponse
    {
        $anggota->update($request->validated());

        return redirect()->route('anggota.show', $anggota)->with('sukses', 'Data anggota berhasil diubah.');
    }

    public function destroy(Anggota $anggota): RedirectResponse
    {
        // BR-07: anggota yang sudah punya riwayat peminjaman cukup dinonaktifkan.
        if ($anggota->peminjaman()->exists()) {
            return back()->with('gagal', 'Anggota tidak bisa dihapus karena sudah punya riwayat peminjaman. Nonaktifkan saja.');
        }

        $anggota->delete();

        return redirect()->route('anggota.index')->with('sukses', 'Anggota berhasil dihapus.');
    }
}
