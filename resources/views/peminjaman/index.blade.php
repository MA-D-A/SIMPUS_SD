@extends('layouts.app')

@section('judul', 'Peminjaman')

@section('aksi')
    <a href="{{ route('peminjaman.create') }}" class="btn btn-primary">+ Peminjaman Baru</a>
@endsection

@section('konten')
    @php($adaFilter = request()->anyFilled(['cari', 'status', 'dari', 'sampai']))

    <form method="GET" role="search" class="mb-4 flex flex-wrap items-end gap-3">
        <div class="w-full sm:w-64">
            <x-field name="cari" label="Cari" type="search" :value="request('cari')" placeholder="Nama, NIS, atau nomor transaksi" />
        </div>
        <div class="w-full sm:w-44">
            <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
            <select id="status" name="status" class="input">
                <option value="">Semua</option>
                <option value="dipinjam" @selected(request('status') === 'dipinjam')>Masih dipinjam</option>
                <option value="terlambat" @selected(request('status') === 'terlambat')>Terlambat</option>
                <option value="selesai" @selected(request('status') === 'selesai')>Selesai</option>
            </select>
        </div>
        <div class="w-full sm:w-44">
            <x-field name="dari" label="Dipinjam dari" type="date" :value="request('dari')" />
        </div>
        <div class="w-full sm:w-44">
            <x-field name="sampai" label="Sampai" type="date" :value="request('sampai')" />
        </div>
        <button type="submit" class="btn btn-secondary">Cari</button>
        @if ($adaFilter)
            <a href="{{ route('peminjaman.index') }}" class="link py-2">Hapus filter</a>
        @endif
    </form>

    <div class="card overflow-x-auto">
        <table class="tabel">
            <thead>
                <tr>
                    <th>No. transaksi</th>
                    <th>Tanggal pinjam</th>
                    <th>Anggota</th>
                    <th>Kelas</th>
                    <th class="text-right">Jumlah buku</th>
                    <th>Jatuh tempo</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($peminjaman as $p)
                    <tr>
                        <td class="whitespace-nowrap"><a href="{{ route('peminjaman.show', $p) }}" class="link font-mono text-xs">{{ $p->kode }}</a></td>
                        <td class="whitespace-nowrap">{{ $p->tanggal_pinjam->format('d/m/Y') }}</td>
                        <td>
                            {{ $p->anggota->nama }}
                            <span class="block text-xs text-slate-500">{{ $p->anggota->nomor_induk }}</span>
                        </td>
                        <td>{{ $p->anggota->label_kelas }}</td>
                        <td class="text-right tabular-nums">{{ $p->detail_count }}</td>
                        <td class="whitespace-nowrap">{{ $p->tanggal_jatuh_tempo->format('d/m/Y') }}</td>
                        <td><x-badge-status :status="$p->statusTampilan()" /></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-500">Tidak ada transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $peminjaman->links() }}</div>
@endsection
