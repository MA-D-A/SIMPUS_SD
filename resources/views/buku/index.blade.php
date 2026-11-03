@extends('layouts.app')

@section('judul', 'Buku')

@section('aksi')
    <a href="{{ route('buku.create') }}" class="btn btn-primary">+ Tambah Buku</a>
@endsection

@section('konten')
    @php($adaFilter = request()->anyFilled(['cari', 'kategori']))

    <form method="GET" role="search" class="mb-4 flex flex-wrap items-end gap-3">
        <div class="w-full sm:w-80">
            <x-field name="cari" label="Cari" type="search" :value="request('cari')" placeholder="Kode, judul, atau pengarang" />
        </div>
        <div class="w-full sm:w-56">
            <label for="kategori" class="block text-sm font-medium text-slate-700">Kategori</label>
            <select id="kategori" name="kategori" class="input">
                <option value="">Semua kategori</option>
                @foreach ($semuaKategori as $k)
                    <option value="{{ $k->id }}" @selected(request('kategori') == $k->id)>{{ $k->nama }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-secondary">Cari</button>
        @if ($adaFilter)
            <a href="{{ route('buku.index') }}" class="link py-2">Hapus filter</a>
        @endif
    </form>

    <div class="card overflow-x-auto">
        <table class="tabel">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Judul</th>
                    <th>Pengarang</th>
                    <th>Kategori</th>
                    <th class="text-right">Tersedia / Jumlah</th>
                    <th><span class="sr-only">Aksi</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($buku as $b)
                    <tr>
                        <td class="font-mono text-xs whitespace-nowrap">{{ $b->kode_buku }}</td>
                        <td><a href="{{ route('buku.show', $b) }}" class="link">{{ $b->judul }}</a></td>
                        <td>{{ $b->pengarang }}</td>
                        <td>{{ $b->kategori->nama }}</td>
                        <td class="text-right whitespace-nowrap tabular-nums">{{ $b->tersedia }} / {{ $b->jumlah }}</td>
                        <td class="text-right whitespace-nowrap">
                            <a href="{{ route('buku.edit', $b) }}" class="link">Ubah</a>
                            <x-tombol-hapus link class="ml-4" :action="route('buku.destroy', $b)" :konfirmasi="'Hapus buku '.$b->judul.'?'" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-500">
                            {{ $adaFilter ? 'Buku tidak ditemukan.' : 'Belum ada data buku.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $buku->links() }}</div>
@endsection
