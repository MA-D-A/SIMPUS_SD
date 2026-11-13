@extends('layouts.app')

@section('judul', $judul)

@section('aksi')
    @include('laporan._ekspor')
@endsection

@section('konten')
    <form method="GET" class="mb-4 flex flex-wrap items-end gap-3">
        <label class="inline-flex min-h-11 items-center gap-3">
            <input type="checkbox" name="terlambat" value="1" class="size-5" @checked($hanyaTerlambat)>
            <span class="text-sm font-medium text-slate-700">Hanya yang terlambat</span>
        </label>
        <button type="submit" class="btn btn-secondary">Tampilkan</button>
    </form>

    @include('laporan._rekap')

    <div class="card overflow-x-auto">
        @include('laporan.tabel.belum-kembali', ['baris' => $halaman, 'awal' => $halaman->firstItem() ?? 1])
    </div>

    <div class="mt-4">{{ $halaman->links() }}</div>
@endsection
