<?php

namespace App\Http\Controllers;

use App\Http\Requests\PeminjamanRequest;
use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Pengaturan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PeminjamanController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
        ], ['sampai.after_or_equal' => 'Tanggal awal harus sebelum tanggal akhir.']);

        $peminjaman = Peminjaman::query()
            ->with('anggota')
            ->withCount('detail')
            ->when($request->input('status'), fn ($query, $status) => match ($status) {
                'dipinjam' => $query->aktif(),
                'terlambat' => $query->terlambat(),
                'selesai' => $query->where('status', 'selesai'),
                default => $query,
            })
            ->when($request->input('dari'), fn ($query, $dari) => $query->whereDate('tanggal_pinjam', '>=', $dari))
            ->when($request->input('sampai'), fn ($query, $sampai) => $query->whereDate('tanggal_pinjam', '<=', $sampai))
            ->when($request->string('cari')->trim()->value(), fn ($query, $cari) => $query->cari($cari))
            ->latest('tanggal_pinjam')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('peminjaman.index', compact('peminjaman'));
    }

    public function create(): View
    {
        return view('peminjaman.create', [
            'pengaturan' => Pengaturan::ambil(),
            'semuaAnggota' => Anggota::aktif()->orderBy('nama')->get(),
            'bukuTersedia' => Buku::denganStok()->orderBy('judul')->get()->filter(fn ($buku) => $buku->tersedia > 0),
        ]);
    }

    public function store(PeminjamanRequest $request): RedirectResponse
    {
        $tanggalPinjam = Carbon::parse($request->validated('tanggal_pinjam'));
        $lamaPinjam = Pengaturan::ambil()->lama_pinjam;

        $peminjaman = DB::transaction(function () use ($request, $tanggalPinjam, $lamaPinjam) {
            // Kunci baris buku lalu cek ulang stok, untuk berjaga-jaga jika form terkirim dua kali.
            $buku = Buku::whereIn('kode_buku', $request->validated('kode_buku'))->lockForUpdate()->get();

            foreach ($buku as $b) {
                if ($b->tersedia <= 0) {
                    throw ValidationException::withMessages(['kode_buku' => "Stok {$b->judul} sedang habis."]);
                }
            }

            $peminjaman = $request->anggota()->peminjaman()->create([
                'tanggal_pinjam' => $tanggalPinjam,
                'tanggal_jatuh_tempo' => $tanggalPinjam->copy()->addDays($lamaPinjam),
                'status' => 'dipinjam',
                'catatan' => $request->validated('catatan'),
            ]);
            $peminjaman->detail()->createMany($buku->map(fn ($b) => ['buku_id' => $b->id])->all());

            return $peminjaman;
        });

        return redirect()->route('peminjaman.show', $peminjaman)
            ->with('sukses', "Peminjaman {$peminjaman->kode} berhasil dicatat.");
    }

    public function show(Peminjaman $peminjaman): View
    {
        $peminjaman->load(['anggota', 'detail.buku']);

        return view('peminjaman.show', compact('peminjaman'));
    }

    public function destroy(Peminjaman $peminjaman): RedirectResponse
    {
        // BR-09: hanya bisa dibatalkan jika belum ada buku yang dikembalikan.
        if (! $peminjaman->bisaDibatalkan()) {
            return back()->with('gagal', 'Peminjaman yang sudah ada pengembaliannya tidak bisa dibatalkan.');
        }

        $peminjaman->delete();

        return redirect()->route('peminjaman.index')->with('sukses', "Peminjaman {$peminjaman->kode} dibatalkan.");
    }

    public function slip(Peminjaman $peminjaman): Response
    {
        $peminjaman->load(['anggota', 'detail.buku']);

        return Pdf::loadView('peminjaman.slip', [
            'peminjaman' => $peminjaman,
            'pdf' => true,
        ])->setPaper('a5', 'portrait')->stream("slip-{$peminjaman->kode}.pdf");
    }
}
