<?php

namespace App\Http\Controllers;

use App\Http\Requests\PengembalianRequest;
use App\Models\Peminjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PengembalianController extends Controller
{
    public function index(Request $request): View
    {
        $peminjaman = Peminjaman::aktif()
            ->with('anggota')
            ->withCount(['detail as belum_kembali_count' => fn ($query) => $query->whereNull('tanggal_kembali')])
            ->when($request->string('cari')->trim()->value(), fn ($query, $cari) => $query->cari($cari))
            ->orderBy('tanggal_jatuh_tempo')
            ->paginate(15)
            ->withQueryString();

        return view('pengembalian.index', compact('peminjaman'));
    }

    public function create(Peminjaman $peminjaman): View|RedirectResponse
    {
        if ($peminjaman->status === 'selesai') {
            return redirect()->route('peminjaman.show', $peminjaman)
                ->with('gagal', 'Semua buku pada peminjaman ini sudah dikembalikan.');
        }

        $peminjaman->load(['anggota', 'detail.buku']);

        return view('pengembalian.create', compact('peminjaman'));
    }

    public function store(PengembalianRequest $request, Peminjaman $peminjaman): RedirectResponse
    {
        $tanggalKembali = Carbon::parse($request->validated('tanggal_kembali'));

        DB::transaction(function () use ($request, $peminjaman, $tanggalKembali) {
            $peminjaman->detail()->whereIn('id', $request->validated('detail_id'))->update(['tanggal_kembali' => $tanggalKembali]);

            if (! $peminjaman->detail()->whereNull('tanggal_kembali')->exists()) {
                $peminjaman->update(['status' => 'selesai']);
            }
        });

        // BR-06: semua buku yang dikembalikan sekaligus punya jatuh tempo dan tanggal kembali yang sama.
        $hariTerlambat = $peminjaman->hariTerlambat($tanggalKembali);
        $pesan = 'Pengembalian berhasil dicatat.'.($hariTerlambat ? " Terlambat $hariTerlambat hari." : '');

        return redirect()->route('peminjaman.show', $peminjaman)->with('sukses', $pesan);
    }
}
