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
        ]]
    ];
@endphp
<aside class="border-b border-slate-200 bg-white lg:fixed lg:inset-y-0 lg:w-60 lg:border-r lg:border-b-0">
    <a href="{{ route('dashboard') }}" class="block px-5 pt-4 pb-3 text-lg font-bold text-primary-strong lg:py-5">
        SIPUS SD
    </a>
    <nav aria-label="Menu utama" class="flex flex-wrap gap-1 px-3 pb-3 lg:flex-col lg:flex-nowrap">
        @foreach ($menu as $grup)
            @if ($grup['judul'])
                <p class="mt-4 hidden px-3 pb-1 text-xs font-semibold tracking-wide text-slate-500 uppercase lg:block">{{ $grup['judul'] }}</p>
            @endif
            @foreach ($grup['item'] as [$label, $route, $pola])
                @php($aktif = request()->routeIs($pola))
                <a href="{{ route($route) }}" @if ($aktif) aria-current="page" @endif
                   class="flex min-h-11 items-center rounded-md px-3 text-sm font-medium {{ $aktif ? 'bg-primary-soft text-primary-strong' : 'text-slate-700 hover:bg-slate-100' }}">
                    {{ $label }}
                </a>
            @endforeach
        @endforeach
    </nav>
</aside>

<div class="lg:pl-60">
    <header class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 bg-white px-6 py-3">
        <p class="text-sm text-slate-600">Perpustakaan {{ $pengaturan->nama_sekolah }}</p>
        <div class="flex flex-wrap items-center gap-2">
            <span class="mr-2 text-sm font-medium">{{ auth()->user()->name }}</span>
            <a href="{{ route('password.edit') }}" class="btn btn-secondary">Ganti Password</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-secondary">Keluar</button>
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
