@extends('layouts.app')

@section('judul', $anggota->exists ? 'Ubah Anggota' : 'Tambah Anggota')

@section('konten')
    <form method="POST" action="{{ $anggota->exists ? route('anggota.update', $anggota) : route('anggota.store') }}" class="card max-w-3xl p-6">
        @csrf
        @if ($anggota->exists)
            @method('PUT')
        @endif

        <div class="grid gap-4 md:grid-cols-2">
            <x-field name="nomor_induk" label="Nomor induk" :value="$anggota->nomor_induk" required maxlength="30" hint="NIS untuk siswa, NIP atau kode sekolah untuk guru." />
            <x-field name="nama" label="Nama lengkap" :value="$anggota->nama" required maxlength="150" />

            <x-field name="jenis" label="Jenis anggota" required>
                <select id="jenis" name="jenis" required class="input" @error('jenis') aria-invalid="true" aria-describedby="jenis-error" @enderror>
                    <option value="siswa" @selected(old('jenis', $anggota->jenis) === 'siswa')>Siswa</option>
                    <option value="guru" @selected(old('jenis', $anggota->jenis) === 'guru')>Guru</option>
                </select>
            </x-field>
            <x-field name="kelas" label="Kelas" :value="$anggota->kelas" maxlength="10" hint="Wajib untuk siswa, contoh: 4A. Kosongkan untuk guru." />

            <x-field name="jenis_kelamin" label="Jenis kelamin" required>
                <select id="jenis_kelamin" name="jenis_kelamin" required class="input" @error('jenis_kelamin') aria-invalid="true" aria-describedby="jenis_kelamin-error" @enderror>
                    <option value="">Pilih</option>
                    <option value="L" @selected(old('jenis_kelamin', $anggota->jenis_kelamin) === 'L')>Laki-laki</option>
                    <option value="P" @selected(old('jenis_kelamin', $anggota->jenis_kelamin) === 'P')>Perempuan</option>
                </select>
            </x-field>
            <x-field name="no_hp" label="Nomor HP" type="tel" :value="$anggota->no_hp" maxlength="20" hint="Nomor anggota atau orang tua (boleh kosong)." />

            @if ($anggota->exists)
                <div class="md:col-span-2">
                    <input type="hidden" name="aktif" value="0">
                    <label class="inline-flex min-h-11 items-center gap-3">
                        <input type="checkbox" name="aktif" value="1" class="size-5 rounded border-slate-300 text-primary" @checked(old('aktif', $anggota->aktif))>
                        <span class="text-sm font-medium text-slate-700">Anggota aktif (boleh meminjam)</span>
                    </label>
                </div>
            @endif
        </div>

        <div class="mt-6 flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ $anggota->exists ? route('anggota.show', $anggota) : route('anggota.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
@endsection
