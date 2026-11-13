{{--
    Layout cetak: slip, versi cetak laporan, dan PDF (dompdf).
    Sengaja memakai CSS biasa, bukan Tailwind, karena dompdf tidak mendukung CSS modern.
    Set $pdf = true saat dirender ke PDF agar dialog cetak dan tombolnya tidak ikut.
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>@yield('judul') · {{ config('app.name') }}</title>
    <style>
        @page { size: @yield('ukuran', 'A4 landscape'); margin: 12mm; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #111; margin: 0; }
        .kop { text-align: center; border-bottom: 2px solid #111; padding-bottom: 6px; margin-bottom: 10px; }
        .kop .nama { font-size: 15px; font-weight: bold; text-transform: uppercase; }
        h1 { font-size: 13px; text-align: center; text-transform: uppercase; margin: 8px 0 2px; }
        .sub { text-align: center; margin: 0 0 10px; }
        table { border-collapse: collapse; }
        table.data { width: 100%; margin: 8px 0; }
        table.data th, table.data td { border: 1px solid #666; padding: 4px 6px; text-align: left; vertical-align: top; }
        table.data th { background: #eee; }
        table.info td { padding: 2px 8px 2px 0; vertical-align: top; }
        table.ttd { width: 100%; margin-top: 20px; }
        table.ttd td { width: 50%; vertical-align: top; }
        .kanan { text-align: right; }
        .ruang-ttd { height: 56px; }
        .tidak-dicetak { margin: 0 0 12px; font-family: Arial, sans-serif; }
        .tidak-dicetak button, .tidak-dicetak a { font-size: 14px; padding: 6px 14px; margin-right: 8px; }
        @media screen { body { margin: 16px; } }
        @media print { .tidak-dicetak { display: none; } }
        @yield('gaya')
    </style>
</head>
<body @unless ($pdf ?? false) onload="window.print()" @endunless>
    @unless ($pdf ?? false)
        <div class="tidak-dicetak">
            <button type="button" onclick="window.print()">Cetak</button>
            <a href="javascript:history.back()">Kembali</a>
        </div>
    @endunless

    <div class="kop">
        <div class="nama">Perpustakaan {{ $pengaturan->nama_sekolah }}</div>
        @if ($pengaturan->alamat_sekolah)
            <div>{{ $pengaturan->alamat_sekolah }}</div>
        @endif
    </div>

    @yield('konten')
</body>
</html>
