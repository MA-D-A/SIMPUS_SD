@extends('layouts.app')

@section('judul', 'Laporan Peminjaman')

@section('aksi')
    @include('laporan._ekspor')
@endsection

@section('konten')
    <form method="GET" class="mb-4 flex flex-wrap items-end gap-3">
        <div class="w-full sm:w-48">
            <x-field name="dari" label="Dari tanggal" type="date" :value="$filter['dari']" required />
        </div>
        <div class="w-full sm:w-48">
            <x-field name="sampai" label="Sampai tanggal" type="date" :value="$filter['sampai']" required />
        </div>
        <button type="submit" class="btn btn-secondary">Tampilkan</button>
    </form>

    @include('laporan._rekap')

    <div class="card overflow-x-auto">
        @include('laporan.tabel.peminjaman', ['baris' => $halaman, 'awal' => $halaman->firstItem() ?? 1])
    </div>

    <div class="mt-4">{{ $halaman->links() }}</div>
@endsection
