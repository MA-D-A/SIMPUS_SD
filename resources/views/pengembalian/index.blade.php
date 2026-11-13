@extends('layouts.app')

@section('judul', 'Pengembalian')

@section('konten')
    <form method="GET" role="search" class="mb-4 flex flex-wrap items-end gap-3">
        <div class="w-full sm:w-80">
            <x-field name="cari" label="Cari peminjaman aktif" type="search" :value="request('cari')" placeholder="Nama, NIS, atau nomor transaksi" />
        </div>
        <button type="submit" class="btn btn-secondary">Cari</button>
        @if (request()->filled('cari'))
            <a href="{{ route('pengembalian.index') }}" class="link py-2">Hapus filter</a>
        @endif
    </form>

    <div class="card overflow-x-auto">
        <table class="tabel">
            <thead>
                <tr>
                    <th>No. transaksi</th>
                    <th>Anggota</th>
                    <th>Kelas</th>
                    <th>Tanggal pinjam</th>
                    <th>Jatuh tempo</th>
                    <th class="text-right">Belum kembali</th>
                    <th>Status</th>
                    <th><span class="sr-only">Aksi</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($peminjaman as $p)
                    <tr>
                        <td class="whitespace-nowrap"><a href="{{ route('peminjaman.show', $p) }}" class="link font-mono text-xs">{{ $p->kode }}</a></td>
                        <td>
                            {{ $p->anggota->nama }}
                            <span class="block text-xs text-slate-500">{{ $p->anggota->nomor_induk }}</span>
                        </td>
                        <td>{{ $p->anggota->label_kelas }}</td>
                        <td class="whitespace-nowrap">{{ $p->tanggal_pinjam->format('d/m/Y') }}</td>
                        <td class="whitespace-nowrap">{{ $p->tanggal_jatuh_tempo->format('d/m/Y') }}</td>
                        <td class="text-right tabular-nums">{{ $p->belum_kembali_count }} buku</td>
                        <td><x-badge-status :status="$p->statusTampilan()" /></td>
                        <td class="text-right"><a href="{{ route('pengembalian.create', $p) }}" class="btn btn-primary">Proses</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-slate-500">Tidak ada peminjaman aktif.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $peminjaman->links() }}</div>
@endsection
