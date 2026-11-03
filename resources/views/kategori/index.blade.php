@extends('layouts.app')

@section('judul', 'Kategori')

@section('aksi')
    <a href="{{ route('kategori.create') }}" class="btn btn-primary">+ Tambah Kategori</a>
@endsection

@section('konten')
    <div class="card overflow-x-auto">
        <table class="tabel">
            <thead>
                <tr>
                    <th>Nama kategori</th>
                    <th class="text-right">Jumlah judul buku</th>
                    <th><span class="sr-only">Aksi</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kategori as $k)
                    <tr>
                        <td class="font-medium">{{ $k->nama }}</td>
                        <td class="text-right tabular-nums">{{ $k->buku_count }}</td>
                        <td class="text-right whitespace-nowrap">
                            <a href="{{ route('kategori.edit', $k) }}" class="link">Ubah</a>
                            <x-tombol-hapus link class="ml-4" :action="route('kategori.destroy', $k)" :konfirmasi="'Hapus kategori '.$k->nama.'?'" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-8 text-center text-slate-500">Belum ada kategori.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $kategori->links() }}</div>
@endsection
