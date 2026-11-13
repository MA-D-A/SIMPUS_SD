@extends('layouts.app')

@section('judul', 'Laporan Inventaris Buku')

@section('aksi')
    @include('laporan._ekspor')
@endsection

@section('konten')
    <form method="GET" class="mb-4 flex flex-wrap items-end gap-3">
        <div class="w-full sm:w-64">
            <label for="kategori" class="block text-sm font-medium text-slate-700">Kategori</label>
            <select id="kategori" name="kategori" class="input">
                <option value="">Semua kategori</option>
                @foreach ($semuaKategori as $k)
                    <option value="{{ $k->id }}" @selected(request('kategori') == $k->id)>{{ $k->nama }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-secondary">Tampilkan</button>
    </form>

    @include('laporan._rekap')

    <div class="card overflow-x-auto">
        @include('laporan.tabel.buku', ['baris' => $halaman, 'awal' => $halaman->firstItem() ?? 1])
    </div>

    <div class="mt-4">{{ $halaman->links() }}</div>
@endsection
