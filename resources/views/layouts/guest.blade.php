<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul') · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    class="relative isolate flex min-h-screen items-center justify-center overflow-x-hidden bg-slate-950 px-4 py-10 text-slate-900 antialiased sm:px-6"
    style="background-image: url('{{ asset('images/perpustakaan.webp') }}'); background-size: cover; background-position: center; background-attachment: fixed;"
>
    {{-- Lapisan kontras menjaga formulir terbaca sekaligus menonjolkan foto perpustakaan. --}}
    <div aria-hidden="true" class="pointer-events-none fixed inset-0 bg-slate-950/30"></div>

    <main class="relative z-10 w-full max-w-md">
        @yield('konten')
    </main>
</body>
</html>
