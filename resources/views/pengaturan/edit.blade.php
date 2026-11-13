@extends('layouts.app')

@section('judul', 'Pengaturan')

@section('konten')
    <form method="POST" action="{{ route('pengaturan.update') }}" class="card max-w-3xl space-y-6 p-6">
        @csrf
        @method('PUT')

        <fieldset class="grid gap-4 md:grid-cols-2">
            <legend class="mb-2 text-lg font-semibold">Identitas sekolah</legend>
            <div class="md:col-span-2">
                <x-field name="nama_sekolah" label="Nama sekolah" :value="$pengaturan->nama_sekolah" required maxlength="150" hint="Tampil di bilah atas, kop slip, dan kop laporan." />
            </div>
            <div class="md:col-span-2">
                <x-field name="alamat_sekolah" label="Alamat sekolah" :value="$pengaturan->alamat_sekolah" maxlength="255" />
            </div>
            <x-field name="nama_kepala_sekolah" label="Nama kepala sekolah" :value="$pengaturan->nama_kepala_sekolah" maxlength="150" hint="Untuk kolom tanda tangan laporan." />
            <x-field name="nip_kepala_sekolah" label="NIP kepala sekolah" :value="$pengaturan->nip_kepala_sekolah" maxlength="30" />
            <div class="md:col-span-2">
                <x-field name="nama_petugas" label="Nama petugas perpustakaan" :value="auth()->user()->name" required maxlength="100"
                         hint="Tampil di bilah atas dan kolom tanda tangan petugas pada slip dan laporan." />
            </div>
        </fieldset>

        <fieldset class="grid gap-4 md:grid-cols-2">
            <legend class="mb-2 text-lg font-semibold">Aturan peminjaman</legend>
            <x-field name="lama_pinjam" label="Lama pinjam (hari)" type="number" :value="$pengaturan->lama_pinjam" required min="1" max="60"
                     hint="Hanya berlaku untuk peminjaman baru; jatuh tempo yang sudah tercatat tidak berubah." />
            <x-field name="maks_pinjam" label="Maksimal buku per anggota" type="number" :value="$pengaturan->maks_pinjam" required min="1" max="10" />
        </fieldset>

        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
@endsection
