@extends('layouts.app')

@section('judul', 'Peminjaman '.$peminjaman->kode)

@section('aksi')
    <a href="{{ route('peminjaman.slip', $peminjaman) }}" target="_blank" class="btn btn-secondary">Cetak Slip</a>
    @if ($peminjaman->status === 'dipinjam')
        <a href="{{ route('pengembalian.create', $peminjaman) }}" class="btn btn-primary">Proses Pengembalian</a>
    @endif
    @if ($peminjaman->bisaDibatalkan())
        <x-tombol-hapus :action="route('peminjaman.destroy', $peminjaman)" label="Batalkan" konfirmasi="Batalkan peminjaman ini?" />
    @endif
@endsection

@section('konten')
    <div class="card mb-6 p-6">
        <dl class="grid gap-x-6 gap-y-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <dt class="text-sm text-slate-500">Anggota</dt>
                <dd class="mt-0.5 font-medium">
                    <a href="{{ route('anggota.show', $peminjaman->anggota) }}" class="link">{{ $peminjaman->anggota->nama }}</a>
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
            <div>
                <dt class="text-sm text-slate-500">Status</dt>
                <dd class="mt-1"><x-badge-status :status="$peminjaman->statusTampilan()" /></dd>
            </div>
            @if ($peminjaman->catatan)
                <div class="sm:col-span-2 lg:col-span-4">
                    <dt class="text-sm text-slate-500">Catatan</dt>
                    <dd class="mt-0.5">{{ $peminjaman->catatan }}</dd>
                </div>
            @endif
        </dl>
    </div>

    <div class="card overflow-x-auto">
        <table class="tabel">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Judul</th>
                    <th>Tanggal kembali</th>
                    <th class="text-right">Hari terlambat</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($peminjaman->detail as $d)
                    <tr>
                        <td class="font-mono text-xs whitespace-nowrap">{{ $d->buku->kode_buku }}</td>
                        <td><a href="{{ route('buku.show', $d->buku) }}" class="link">{{ $d->buku->judul }}</a></td>
                        <td class="whitespace-nowrap">{{ $d->tanggal_kembali?->translatedFormat('j F Y') ?? 'Belum kembali' }}</td>
                        <td class="text-right tabular-nums {{ $d->hariTerlambat() ? 'font-semibold text-red-700' : '' }}">{{ $d->hariTerlambat() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
