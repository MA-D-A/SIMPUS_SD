{{-- Dipakai di layar (class tabel, Tailwind) dan di cetak/PDF (class data, CSS layout cetak). --}}
<table class="tabel data">
    <thead>
        <tr>
            <th>No</th>
            <th>Kode</th>
            <th>Judul</th>
            <th>Pengarang</th>
            <th>Penerbit</th>
            <th>Tahun</th>
            <th>Kategori</th>
            <th class="kanan text-right">Jumlah</th>
            <th class="kanan text-right">Dipinjam</th>
            <th class="kanan text-right">Tersedia</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($baris as $b)
            <tr>
                <td>{{ $awal + $loop->index }}</td>
                <td>{{ $b->kode_buku }}</td>
                <td>{{ $b->judul }}</td>
                <td>{{ $b->pengarang }}</td>
                <td>{{ $b->penerbit ?? '-' }}</td>
                <td>{{ $b->tahun_terbit ?? '-' }}</td>
                <td>{{ $b->kategori->nama }}</td>
                <td class="kanan text-right tabular-nums">{{ $b->jumlah }}</td>
                <td class="kanan text-right tabular-nums">{{ $b->dipinjam_count }}</td>
                <td class="kanan text-right tabular-nums">{{ $b->tersedia }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="10">Tidak ada data untuk filter ini.</td>
            </tr>
        @endforelse
    </tbody>
</table>
