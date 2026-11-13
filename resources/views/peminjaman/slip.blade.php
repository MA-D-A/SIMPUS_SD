@extends('layouts.cetak')

@section('judul', 'Slip '.$peminjaman->kode)
@section('ukuran', 'A5 portrait')

@section('konten')
    <h1>Slip Peminjaman Buku</h1>

    <table class="info">
        <tr><td>No. transaksi</td><td>: {{ $peminjaman->kode }}</td></tr>
        <tr><td>Nama</td><td>: {{ $peminjaman->anggota->nama }}</td></tr>
        <tr><td>Nomor induk / Kelas</td><td>: {{ $peminjaman->anggota->nomor_induk }} / {{ $peminjaman->anggota->label_kelas }}</td></tr>
        <tr><td>Tanggal pinjam</td><td>: {{ $peminjaman->tanggal_pinjam->translatedFormat('j F Y') }}</td></tr>
        <tr><td>Jatuh tempo</td><td>: <strong>{{ $peminjaman->tanggal_jatuh_tempo->translatedFormat('j F Y') }}</strong></td></tr>
    </table>

    <table class="data">
        <thead>
            <tr><th>No</th><th>Kode</th><th>Judul</th></tr>
        </thead>
        <tbody>
            @foreach ($peminjaman->detail as $d)
                <tr><td>{{ $loop->iteration }}</td><td>{{ $d->buku->kode_buku }}</td><td>{{ $d->buku->judul }}</td></tr>
            @endforeach
        </tbody>
    </table>

    <p>Kembalikan buku paling lambat pada tanggal jatuh tempo. Jagalah buku tetap bersih.</p>

    <table class="ttd">
        <tr>
            <td>Peminjam,<div class="ruang-ttd"></div>({{ $peminjaman->anggota->nama }})</td>
            <td>Petugas,<div class="ruang-ttd"></div>({{ auth()->user()->name }})</td>
        </tr>
    </table>
@endsection
