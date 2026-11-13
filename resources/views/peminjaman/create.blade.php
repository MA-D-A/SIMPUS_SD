@extends('layouts.app')

@section('judul', 'Peminjaman Baru')

@section('konten')
    @php($pesanBuku = collect($errors->get('kode_buku'))->merge(collect($errors->get('kode_buku.*'))->flatten()))

    <form method="POST" action="{{ route('peminjaman.store') }}" class="card max-w-3xl space-y-6 p-6">
        @csrf

        <x-field name="nomor_induk" label="Anggota (ketik NIS atau nama)" required>
            <input id="nomor_induk" name="nomor_induk" list="daftar-anggota" value="{{ old('nomor_induk') }}" required autocomplete="off" autofocus class="input"
                   @error('nomor_induk') aria-invalid="true" aria-describedby="nomor_induk-error" @enderror>
        </x-field>

        <fieldset>
            <legend class="text-sm font-medium text-slate-700">
                Buku yang dipinjam (ketik kode atau judul) <span class="text-red-700" aria-hidden="true">*</span>
            </legend>
            <p class="mt-1 text-sm text-slate-500">Maksimal {{ $pengaturan->maks_pinjam }} buku per anggota. Saran hanya menampilkan buku yang tersedia.</p>
            <div class="mt-2 space-y-3">
                @for ($i = 0; $i < $pengaturan->maks_pinjam; $i++)
                    <div>
                        <label for="kode_buku_{{ $i }}" class="sr-only">Buku {{ $i + 1 }}</label>
                        <input id="kode_buku_{{ $i }}" name="kode_buku[]" list="daftar-buku" value="{{ old('kode_buku.'.$i) }}" autocomplete="off" class="input"
                               placeholder="Buku {{ $i + 1 }}" @required($i === 0)
                               @if ($pesanBuku->isNotEmpty()) aria-invalid="true" aria-describedby="kode_buku-error" @endif>
                    </div>
                @endfor
            </div>
            @if ($pesanBuku->isNotEmpty())
                <div id="kode_buku-error" class="mt-2 space-y-1">
                    @foreach ($pesanBuku as $pesan)
                        <p class="text-sm text-red-700">{{ $pesan }}</p>
                    @endforeach
                </div>
            @endif
        </fieldset>

        <div class="grid gap-4 md:grid-cols-2">
            <x-field name="tanggal_pinjam" label="Tanggal pinjam" type="date" :value="today()->toDateString()" :max="today()->toDateString()" required
                     :hint="'Jatuh tempo otomatis '.$pengaturan->lama_pinjam.' hari setelah tanggal pinjam.'" />
            <x-field name="catatan" label="Catatan" maxlength="255" hint="Boleh kosong." />
        </div>

        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan Peminjaman</button>
            <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>

    <datalist id="daftar-anggota">
        @foreach ($semuaAnggota as $a)
            <option value="{{ $a->nomor_induk }} - {{ $a->nama }} ({{ $a->kelas ?? 'guru' }})"></option>
        @endforeach
    </datalist>
    <datalist id="daftar-buku">
        @foreach ($bukuTersedia as $b)
            <option value="{{ $b->kode_buku }} - {{ $b->judul }}"></option>
        @endforeach
    </datalist>
@endsection
