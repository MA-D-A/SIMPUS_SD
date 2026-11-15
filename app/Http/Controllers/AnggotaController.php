<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnggotaImportRequest;
use App\Http\Requests\AnggotaRequest;
use App\Models\Anggota;
use App\Services\AnggotaImporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\SimpleExcel\SimpleExcelWriter;

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

    public function importForm(): View
    {
        return view('anggota.import');
    }

    public function importStore(AnggotaImportRequest $request, AnggotaImporter $importer): RedirectResponse
    {
        $berkas = $request->file('file');
        $ext = strtolower($berkas->getClientOriginalExtension());
        $path = $berkas->storeAs('import', Str::uuid().'.'.$ext);

        $hasil = $importer->import(Storage::path($path));

        Storage::delete($path);

        return redirect()
            ->route('anggota.import')
            ->with('hasil', $hasil);
    }

    public function importTemplate()
    {
        $path = storage_path('app/'.Str::uuid().'.xlsx');

        $writer = SimpleExcelWriter::create($path)
            ->addHeader(['nomor_induk', 'nama', 'jenis', 'kelas', 'jenis_kelamin', 'no_hp']);
        $writer->close();

        return response()->download($path, 'template-anggota.xlsx')->deleteFileAfterSend();
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
