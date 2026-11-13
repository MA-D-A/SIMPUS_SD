@extends('layouts.app')

@section('judul', $buku->exists ? 'Ubah Buku' : 'Tambah Buku')

@section('konten')
    <form method="POST" action="{{ $buku->exists ? route('buku.update', $buku) : route('buku.store') }}" class="card max-w-3xl p-6">
        @csrf
        @if ($buku->exists)
            @method('PUT')
        @endif

        <div class="grid gap-4 md:grid-cols-2">
            <x-field name="kode_buku" label="Kode buku" :value="$buku->kode_buku" required maxlength="20" hint="Kode yang tertempel di label buku." />

            <x-field name="kategori_id" label="Kategori" required>
                <select id="kategori_id" name="kategori_id" required class="input"
                        @error('kategori_id') aria-invalid="true" aria-describedby="kategori_id-error" @enderror>
                    <option value="">Pilih kategori</option>
                    @foreach ($semuaKategori as $k)
                        <option value="{{ $k->id }}" @selected(old('kategori_id', $buku->kategori_id) == $k->id)>{{ $k->nama }}</option>
                    @endforeach
                </select>
                @if ($semuaKategori->isEmpty())
                    <p class="mt-1 text-sm text-slate-600">
                        Belum ada kategori. <a href="{{ route('kategori.create') }}" class="link">Tambah kategori</a> dulu.
                    </p>
                @endif
            </x-field>

            <div class="md:col-span-2">
                <x-field name="judul" label="Judul" :value="$buku->judul" required maxlength="255" />
            </div>
            <x-field name="pengarang" label="Pengarang" :value="$buku->pengarang" required maxlength="150" />
            <x-field name="penerbit" label="Penerbit" :value="$buku->penerbit" maxlength="150" />
            <x-field name="tahun_terbit" label="Tahun terbit" type="number" :value="$buku->tahun_terbit" min="1901" :max="now()->year" />
            <x-field name="isbn" label="ISBN" :value="$buku->isbn" maxlength="20" />
            <x-field name="kondisi" label="Kondisi" required>
                <select id="kondisi" name="kondisi" required class="input"
                        @error('kondisi') aria-invalid="true" aria-describedby="kondisi-error" @enderror>
                    @foreach (['Baik', 'Rusak Ringan', 'Rusak Berat'] as $k)
                        <option value="{{ $k }}" @selected(old('kondisi', $buku->kondisi ?? 'Baik') === $k)>{{ $k }}</option>
                    @endforeach
                </select>
            </x-field>
            <x-field name="jumlah" label="Jumlah eksemplar" type="number" :value="$buku->jumlah" required min="0" max="9999" />
        </div>

        <div class="mt-6 flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ $buku->exists ? route('buku.show', $buku) : route('buku.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
@endsection
