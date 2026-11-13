<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul') · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-white px-4 py-10 text-slate-900 antialiased">
    {{-- Saat gambar perpustakaan siap, pindahkan style ini ke <body> dan ganti URL:
         style="background-image: url('/images/perpustakaan.jpg'); background-size: cover; background-position: center;" --}}
    <main class="w-full max-w-sm">
        @yield('konten')
    </main>
</body>
</html>
