@extends('layouts.cetak')

@section('judul', $judul)

@if ($pdf ?? false)
    {{-- dompdf menata ulang seluruh tabel setiap pindah halaman, sehingga satu tabel panjang sangat lambat dan boros
         memori. Untuk PDF, tabel dipecah per 40 baris; lebar kolom (persen) ditetapkan agar kolom antarpotongan rata. --}}
    @section('gaya')
        table.data { table-layout: fixed; margin: 0; }
        table.data td { word-wrap: break-word; }
        @foreach ([
            'peminjaman' => [3, 7, 8, 14, 13, 4, 6, 15, 8, 8, 7, 7],
            'belum-kembali' => [3, 8, 14, 16, 5, 7, 23, 8, 8, 8],
            'buku' => [4, 7, 22, 14, 12, 5, 12, 6, 6, 6, 6],
        ][$jenis] as $i => $lebar)
            table.data th:nth-child({{ $i + 1 }}) { width: {{ $lebar }}%; }
        @endforeach
    @endsection
@endif

@section('konten')
    <h1>{{ $judul }}</h1>
    <p class="sub">{{ $keterangan }}</p>

    @foreach (($pdf ?? false) && $baris->isNotEmpty() ? $baris->chunk(40) : [$baris] as $potong)
        @include("laporan.tabel.$jenis", ['baris' => $potong, 'awal' => $potong->keys()->first() + 1])
    @endforeach

    <p>
        <strong>Rekap:</strong>
        @foreach ($rekap as $label => $nilai)
            {{ $label }} {{ number_format($nilai, 0, ',', '.') }}@if (! $loop->last) · @endif
        @endforeach
    </p>

    <table class="ttd">
        <tr>
            <td>
                Mengetahui,<br>Kepala Sekolah
                <div class="ruang-ttd"></div>
                {{ $pengaturan->nama_kepala_sekolah ?? '(..............................)' }}
                @if ($pengaturan->nip_kepala_sekolah)
                    <br>NIP {{ $pengaturan->nip_kepala_sekolah }}
                @endif
            </td>
            <td>
                {{ today()->translatedFormat('j F Y') }}<br>Petugas Perpustakaan
                <div class="ruang-ttd"></div>
                {{ auth()->user()->name }}
            </td>
        </tr>
    </table>
@endsection
