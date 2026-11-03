<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\DetailPeminjaman;
use App\Models\Peminjaman;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'jumlahJudul' => Buku::count(),
            'jumlahEksemplar' => (int) Buku::sum('jumlah'),
            'anggotaAktif' => Anggota::aktif()->count(),
            'sedangDipinjam' => DetailPeminjaman::belumKembali()->count(),
            'jumlahTerlambat' => DetailPeminjaman::terlambat()->count(),
            'terlambat' => DetailPeminjaman::terlambat()->with(['buku', 'peminjaman.anggota'])->urutJatuhTempo()->take(10)->get(),
            'terbaru' => Peminjaman::with('anggota')->withCount('detail')->latest('id')->take(5)->get(),
        ]);
    }
}
    