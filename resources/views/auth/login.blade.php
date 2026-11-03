@extends('layouts.guest')

@section('judul', 'Masuk')

@section('konten')
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-primary-strong">SIPUS SD</h1>
        <p class="mt-1 text-sm text-slate-600">Sistem Informasi Perpustakaan {{ $pengaturan->nama_sekolah }}</p>
    </div>

    <div class="card p-6">
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
    </div>
@endsection
