@extends('layouts.app')

@section('judul', $anggota->nama)

@section('aksi')
    <a href="{{ route('anggota.edit', $anggota) }}" class="btn btn-secondary">Ubah</a>
    <x-tombol-hapus :action="route('anggota.destroy', $anggota)" :konfirmasi="'Hapus anggota '.$anggota->nama.'?'" />
@endsection

@section('konten')
    <div class="card max-w-3xl p-6">
        <dl class="grid gap-x-6 gap-y-4 sm:grid-cols-2">
            @foreach ([
                'Nomor induk' => $anggota->nomor_induk,
                'Jenis anggota' => ucfirst($anggota->jenis),
                'Kelas' => $anggota->kelas,
                'Jenis kelamin' => $anggota->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
                'Nomor HP' => $anggota->no_hp,
            ] as $label => $isi)
                <div>
                    <dt class="text-sm text-slate-500">{{ $label }}</dt>
                    <dd class="mt-0.5 font-medium">{{ $isi ?? '–' }}</dd>
                </div>
            @endforeach
            <div>
                <dt class="text-sm text-slate-500">Status</dt>
                <dd class="mt-1"><x-badge-status :status="$anggota->aktif ? 'aktif' : 'nonaktif'" /></dd>
            </div>
        </dl>
    </div>

    <h2 class="mt-8 mb-3 text-lg font-semibold">Sedang dipinjam</h2>
    <div class="card overflow-x-auto">
        <table class="tabel">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>No. transaksi</th>
                    <th>Jatuh tempo</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sedangDipinjam as $d)
                    <tr>
                        <td><a href="{{ route('buku.show', $d->buku) }}" class="link">{{ $d->buku->judul }}</a></td>
                        <td class="whitespace-nowrap"><a href="{{ route('peminjaman.show', $d->peminjaman) }}" class="link font-mono text-xs">{{ $d->peminjaman->kode }}</a></td>
                        <td class="whitespace-nowrap">{{ $d->peminjaman->tanggal_jatuh_tempo->format('d/m/Y') }}</td>
                        <td><x-badge-status :status="$d->peminjaman->statusTampilan()" /></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-6 text-center text-slate-500">Tidak sedang meminjam buku.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <h2 class="mt-8 mb-3 text-lg font-semibold">Riwayat peminjaman terakhir</h2>
    <div class="card overflow-x-auto">
        <table class="tabel">
            <thead>
                <tr>
                    <th>No. transaksi</th>
                    <th>Tanggal pinjam</th>
                    <th class="text-right">Jumlah buku</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayat as $p)
                    <tr>
                        <td class="whitespace-nowrap"><a href="{{ route('peminjaman.show', $p) }}" class="link font-mono text-xs">{{ $p->kode }}</a></td>
                        <td class="whitespace-nowrap">{{ $p->tanggal_pinjam->format('d/m/Y') }}</td>
                        <td class="text-right tabular-nums">{{ $p->detail_count }}</td>
                        <td><x-badge-status :status="$p->statusTampilan()" /></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-6 text-center text-slate-500">Belum pernah meminjam.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
