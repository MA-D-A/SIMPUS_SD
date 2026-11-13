@extends('layouts.app')

@section('judul', 'Laporan')

@section('konten')
    <div class="grid gap-4 md:grid-cols-3">
        @foreach ([
            ['laporan.peminjaman', 'Laporan Peminjaman', 'Semua peminjaman dalam satu periode beserta rekapnya.'],
            ['laporan.belum-kembali', 'Laporan Buku Belum Kembali', 'Buku yang masih dipinjam, termasuk yang terlambat.'],
            ['laporan.buku', 'Laporan Inventaris Buku', 'Daftar koleksi beserta jumlah, dipinjam, dan tersedia.'],
        ] as [$route, $nama, $isi])
            <a href="{{ route($route) }}" class="card block p-5 transition-colors duration-150 hover:border-primary/40 hover:bg-primary-soft/40">
                <h2 class="font-semibold text-primary-strong">{{ $nama }}</h2>
                <p class="mt-1 text-sm text-slate-600">{{ $isi }}</p>
            </a>
        @endforeach
    </div>
@endsection
