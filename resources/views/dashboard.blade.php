@extends('layouts.app')

@section('judul', 'Dashboard')

@section('aksi')
    <a href="{{ route('peminjaman.create') }}" class="btn btn-primary">+ Peminjaman Baru</a>
    <a href="{{ route('pengembalian.index') }}" class="btn btn-secondary">Pengembalian</a>
@endsection

@section('konten')
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <x-kartu-statistik label="Judul buku" :nilai="$jumlahJudul" :href="route('buku.index')" />
        <x-kartu-statistik label="Eksemplar" :nilai="$jumlahEksemplar" keterangan="Total semua salinan" :href="route('laporan.buku')" />
        <x-kartu-statistik label="Anggota aktif" :nilai="$anggotaAktif" :href="route('anggota.index', ['status' => 'aktif'])" />
        <x-kartu-statistik label="Sedang dipinjam" :nilai="$sedangDipinjam" keterangan="Buku belum kembali" :href="route('laporan.belum-kembali')" />
        <x-kartu-statistik label="Terlambat" :nilai="$jumlahTerlambat" :kritis="$jumlahTerlambat > 0"
                           :keterangan="$jumlahTerlambat ? 'Perlu segera dikembalikan' : 'Tidak ada'" :href="route('laporan.belum-kembali', ['terlambat' => 1])" />
    </div>

    <h2 class="mt-8 mb-3 text-lg font-semibold">Keterlambatan terlama</h2>
    <div class="card overflow-x-auto">
        <table class="tabel">
            <thead>
                <tr>
                    <th>Anggota</th>
                    <th>Kelas</th>
                    <th>Buku</th>
                    <th>Jatuh tempo</th>
                    <th class="text-right">Terlambat</th>
                    <th><span class="sr-only">Aksi</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($terlambat as $d)
                    <tr>
                        <td>{{ $d->peminjaman->anggota->nama }}</td>
                        <td>{{ $d->peminjaman->anggota->label_kelas }}</td>
                        <td>{{ $d->buku->judul }}</td>
                        <td class="whitespace-nowrap">{{ $d->peminjaman->tanggal_jatuh_tempo->format('d/m/Y') }}</td>
                        <td class="text-right font-semibold whitespace-nowrap text-red-700 tabular-nums">{{ $d->hariTerlambat() }} hari</td>
                        <td class="text-right"><a href="{{ route('pengembalian.create', $d->peminjaman) }}" class="link">Proses</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-slate-500">Tidak ada buku yang terlambat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <h2 class="mt-8 mb-3 text-lg font-semibold">Transaksi terbaru</h2>
    <div class="card overflow-x-auto">
        <table class="tabel">
            <thead>
                <tr>
                    <th>No. transaksi</th>
                    <th>Anggota</th>
                    <th>Tanggal pinjam</th>
                    <th class="text-right">Jumlah buku</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($terbaru as $p)
                    <tr>
                        <td class="whitespace-nowrap"><a href="{{ route('peminjaman.show', $p) }}" class="link font-mono text-xs">{{ $p->kode }}</a></td>
                        <td>{{ $p->anggota->nama }}</td>
                        <td class="whitespace-nowrap">{{ $p->tanggal_pinjam->format('d/m/Y') }}</td>
                        <td class="text-right tabular-nums">{{ $p->detail_count }}</td>
                        <td><x-badge-status :status="$p->statusTampilan()" /></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-slate-500">Belum ada transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
