<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul') · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    class="relative flex min-h-screen items-center justify-center bg-slate-800 px-4 py-10 text-slate-900 antialiased"
    style="background-image: url('{{ asset('images/perpustakaan.webp') }}'); background-size: cover; background-position: center; background-attachment: fixed;"
>
    {{-- Lapisan gelap tipis supaya konten tetap mudah dibaca di atas foto --}}
    <div class="pointer-events-none fixed inset-0 bg-slate-900/40"></div>

    <main class="relative z-10 w-full max-w-sm">
        @yield('konten')
    </main>
</body>
</html>