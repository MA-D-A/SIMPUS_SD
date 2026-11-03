```blade
@extends('layouts.app')

@section('judul', $buku->judul)

@section('aksi')
    <a href="{{ route('buku.edit', $buku) }}" class="btn btn-secondary">Ubah</a>
    <x-tombol-hapus :action="route('buku.destroy', $buku)" :konfirmasi="'Hapus buku '.$buku->judul.'?'" />
@endsection

@section('konten')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="card p-6 lg:col-span-2">
            <dl class="grid gap-x-6 gap-y-4 sm:grid-cols-2">
                @foreach ([
                    'Kode buku' => $buku->kode_buku,
                    'Kategori' => $buku->kategori->nama,
                    'Pengarang' => $buku->pengarang,
                    'Penerbit' => $buku->penerbit,
                    'Tahun terbit' => $buku->tahun_terbit,
                    'ISBN' => $buku->isbn,
                    'Kondisi' => $buku->kondisi,
                ] as $label => $isi)
                    <div>
                        <dt class="text-sm text-slate-500">{{ $label }}</dt>
                        <dd class="mt-0.5 font-medium">{{ $isi ?? '–' }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>

        <div class="card p-6">
            <h2 class="text-sm font-semibold text-slate-500">Stok</h2>
            <dl class="mt-3 space-y-2">
                <div class="flex justify-between">
                    <dt>Jumlah eksemplar</dt>
                    <dd class="font-semibold tabular-nums">{{ $buku->jumlah }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt>Sedang dipinjam</dt>
                    <dd class="font-semibold tabular-nums">{{ $buku->jumlahDipinjam() }}</dd>
                </div>
                <div class="flex justify-between border-t border-slate-100 pt-2">
                    <dt>Tersedia</dt>
                    <dd class="text-lg font-bold tabular-nums">{{ $buku->tersedia }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <h2 class="mt-8 mb-3 text-lg font-semibold">Riwayat peminjaman terakhir</h2>
    <div class="card overflow-x-auto">
        <table class="tabel">
            <thead>
                <tr>
                    <th>No. transaksi</th>
                    <th>Anggota</th>
                    <th>Tanggal pinjam</th>
                    <th>Tanggal kembali</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayat as $d)
                    <tr>
                        <td class="whitespace-nowrap"><a href="{{ route('peminjaman.show', $d->peminjaman) }}" class="link font-mono text-xs">{{ $d->peminjaman->kode }}</a></td>
                        <td>{{ $d->peminjaman->anggota->nama }}</td>
                        <td class="whitespace-nowrap">{{ $d->peminjaman->tanggal_pinjam->format('d/m/Y') }}</td>
                        <td class="whitespace-nowrap">{{ $d->tanggal_kembali?->format('d/m/Y') ?? 'Belum kembali' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-6 text-center text-slate-500">Buku ini belum pernah dipinjam.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
```