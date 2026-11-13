@extends('layouts.app')

@section('judul', 'Pengembalian '.$peminjaman->kode)

@section('konten')
    @php
        $belumKembali = $peminjaman->detail->reject->sudahKembali();
        $sudahKembali = $peminjaman->detail->count() - $belumKembali->count();
        $pesanBuku = collect($errors->get('detail_id'))->merge(collect($errors->get('detail_id.*'))->flatten());
    @endphp

    <form method="POST" action="{{ route('pengembalian.store', $peminjaman) }}" class="card max-w-4xl space-y-6 p-6">
        @csrf

        <dl class="grid gap-x-6 gap-y-3 sm:grid-cols-3">
            <div>
                <dt class="text-sm text-slate-500">Anggota</dt>
                <dd class="mt-0.5 font-medium">{{ $peminjaman->anggota->nama }}
                    <span class="block text-sm font-normal text-slate-500">{{ $peminjaman->anggota->nomor_induk }} · {{ $peminjaman->anggota->label_kelas }}</span>
                </dd>
            </div>
            <div>
                <dt class="text-sm text-slate-500">Tanggal pinjam</dt>
                <dd class="mt-0.5 font-medium">{{ $peminjaman->tanggal_pinjam->translatedFormat('j F Y') }}</dd>
            </div>
            <div>
                <dt class="text-sm text-slate-500">Jatuh tempo</dt>
                <dd class="mt-0.5 font-medium">{{ $peminjaman->tanggal_jatuh_tempo->translatedFormat('j F Y') }}</dd>
            </div>
        </dl>

        <fieldset>
            <legend class="text-sm font-medium text-slate-700">Centang buku yang dikembalikan</legend>
            @if ($sudahKembali)
                <p class="mt-1 text-sm text-slate-500">{{ $sudahKembali }} buku lain sudah dikembalikan sebelumnya.</p>
            @endif
            <div class="mt-2 overflow-x-auto rounded-md border border-slate-200">
                <table class="tabel">
                    <thead>
                        <tr>
                            <th><span class="sr-only">Dikembalikan</span></th>
                            <th>Kode</th>
                            <th>Judul</th>
                            <th class="text-right">Terlambat (per hari ini)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($belumKembali as $d)
                            <tr>
                                <td class="w-12">
                                    <input id="detail_{{ $d->id }}" type="checkbox" name="detail_id[]" value="{{ $d->id }}" class="size-5"
                                           @checked(in_array($d->id, old('detail_id', $belumKembali->pluck('id')->all())))
                                           @if ($pesanBuku->isNotEmpty()) aria-describedby="detail_id-error" @endif>
                                </td>
                                <td class="font-mono text-xs whitespace-nowrap">{{ $d->buku->kode_buku }}</td>
                                <td><label for="detail_{{ $d->id }}">{{ $d->buku->judul }}</label></td>
                                <td class="text-right tabular-nums {{ $d->hariTerlambat() ? 'font-semibold text-red-700' : '' }}">{{ $d->hariTerlambat() }} hari</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($pesanBuku->isNotEmpty())
                <div id="detail_id-error" class="mt-2 space-y-1">
                    @foreach ($pesanBuku as $pesan)
                        <p class="text-sm text-red-700">{{ $pesan }}</p>
                    @endforeach
                </div>
            @endif
        </fieldset>

        <div class="max-w-xs">
            <x-field name="tanggal_kembali" label="Tanggal kembali" type="date" :value="today()->toDateString()"
                     :min="$peminjaman->tanggal_pinjam->toDateString()" :max="today()->toDateString()" required />
        </div>

        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan Pengembalian</button>
            <a href="{{ route('pengembalian.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
@endsection
