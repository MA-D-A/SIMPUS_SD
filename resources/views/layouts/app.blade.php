<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul') · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
@php
    // [label, nama route, pola route untuk menandai menu aktif]
    $menu = [
        ['judul' => null, 'item' => [
            ['Dashboard', 'dashboard', 'dashboard'],
        ]],
        ['judul' => 'Master Data', 'item' => [
            ['Kategori', 'kategori.index', 'kategori.*'],
             ['Buku', 'buku.index', 'buku.*'],
             ['Anggota', 'anggota.index', 'anggota.*'],
        ]],
        ['judul' => 'Transaksi', 'item' => [
            ['Peminjaman', 'peminjaman.index', 'peminjaman.*'],
            ['Pengembalian', 'pengembalian.index', 'pengembalian.*']
        ]],
          ['judul' => 'Lainnya', 'item' => [
            ['Pengaturan', 'pengaturan.edit', 'pengaturan.*'],
        ]],
    ];
@endphp
<aside class="border-b border-slate-200 bg-white lg:fixed lg:inset-y-0 lg:w-60 lg:border-r lg:border-b-0">
    <a href="{{ route('dashboard') }}" class="block border-b border-slate-200 px-5 py-5 text-primary-strong">
        <span class="block text-lg font-bold">SIPUS SD</span>
        <span class="mt-1 block text-sm font-normal text-slate-600">Perpustakaan sekolah</span>
    </a>
    <nav aria-label="Menu utama" class="flex flex-wrap gap-2 px-3 py-4 lg:flex-col lg:flex-nowrap lg:gap-1 lg:px-4">
        @foreach ($menu as $grup)
            @if ($grup['judul'])
                <p class="mt-3 hidden px-3 pb-2 text-sm font-semibold text-primary-strong lg:block">{{ $grup['judul'] }}</p>
            @endif
            @foreach ($grup['item'] as [$label, $route, $pola])
                @php($aktif = request()->routeIs($pola))
                <a href="{{ route($route) }}" @if ($aktif) aria-current="page" @endif
                   class="flex min-h-11 items-center rounded-md px-3 text-sm font-medium transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary {{ $aktif ? 'bg-slate-100 font-semibold text-primary-strong' : 'text-slate-700 hover:bg-slate-50 hover:text-primary-strong' }}">
                    {{ $label }}
                </a>
            @endforeach
        @endforeach
    </nav>
</aside>

<div class="lg:pl-60">
    <header class="flex flex-wrap items-center justify-between gap-3 bg-primary px-6 py-3 text-white">
        <p class="text-sm font-medium">Perpustakaan {{ $pengaturan->nama_sekolah }}</p>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('password.edit') }}" class="btn rounded-md border border-white/50 bg-white/10 text-white transition-colors hover:bg-white hover:text-primary-strong focus-visible:bg-white focus-visible:text-primary-strong focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">Ganti Password</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn rounded-md border border-white/50 bg-white/10 text-white transition-colors hover:border-red-500 hover:bg-red-500 hover:text-white focus-visible:border-red-500 focus-visible:bg-red-500 focus-visible:text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">Keluar</button>
            </form>
        </div>
    </header>

    <main class="px-6 py-6">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-2xl font-bold tracking-tight">@yield('judul')</h1>
            <div class="flex flex-wrap gap-2">@yield('aksi')</div>
        </div>

        <x-alert />

        @yield('konten')
    </main>
</div>
</body>
</html>
