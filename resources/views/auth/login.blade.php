@extends('layouts.guest')

@section('judul', 'Masuk')

@section('konten')
    <section class="login-panel border-2 border-primary font-sans">
        <header class="mb-7 text-center">
            <h1 class="text-3xl font-bold tracking-tight text-primary-strong">SIPUS SD</h1>
            <p class="mx-auto mt-2 max-w-xs text-base leading-relaxed text-slate-700">
                <span class="block">Sistem Informasi Perpustakaan</span>
                <span class="block">{{ $pengaturan->nama_sekolah }}</span>
            </p>
        </header>

        @if (session('sukses'))
            <p role="status" class="mb-4 rounded-md border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-800">
                {{ session('sukses') }}
            </p>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
            @csrf
            <x-field name="username" label="Username" required autofocus autocomplete="username" />
            <x-field name="password" label="Password" type="password" required autocomplete="current-password" />
            <button type="submit" class="btn btn-primary w-full">Masuk</button>
        </form>
    </section>
@endsection
