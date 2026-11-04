@extends('layouts.app')

@section('judul', $kategori->exists ? 'Ubah Kategori' : 'Tambah Kategori')

@section('konten')
    <form method="POST" action="{{ $kategori->exists ? route('kategori.update', $kategori) : route('kategori.store') }}" class="card max-w-lg space-y-4 p-6">
        @csrf
        @if ($kategori->exists)
            @method('PUT')
        @endif

        <x-field name="nama" label="Nama kategori" :value="$kategori->nama" required maxlength="100" />

        <div class="flex gap-2 pt-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('kategori.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
@endsection
