{{-- Dipakai di layar (class tabel, Tailwind) dan di cetak/PDF (class data, CSS layout cetak). --}}
<table class="tabel data">
    <thead>
        <tr>
            <th>No</th>
            <th>No. transaksi</th>
            <th>NIS</th>
            <th>Nama</th>
            <th>Kelas</th>
            <th>Kode buku</th>
            <th>Judul</th>
            <th>Tgl pinjam</th>
            <th>Jatuh tempo</th>
            <th class="kanan text-right">Terlambat (hari)</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($baris as $d)
            <tr>
                <td>{{ $awal + $loop->index }}</td>
                <td>{{ $d->peminjaman->kode }}</td>
                <td>{{ $d->peminjaman->anggota->nomor_induk }}</td>
                <td>{{ $d->peminjaman->anggota->nama }}</td>
                <td>{{ $d->peminjaman->anggota->label_kelas }}</td>
                <td>{{ $d->buku->kode_buku }}</td>
                <td>{{ $d->buku->judul }}</td>
                <td>{{ $d->peminjaman->tanggal_pinjam->format('d/m/Y') }}</td>
                <td>{{ $d->peminjaman->tanggal_jatuh_tempo->format('d/m/Y') }}</td>
                <td class="kanan text-right tabular-nums {{ $d->hariTerlambat() ? 'font-semibold text-red-700' : '' }}">{{ $d->hariTerlambat() }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="10">Tidak ada data untuk filter ini.</td>
            </tr>
        @endforelse
    </tbody>
</table>
