@extends('layouts.app')

@section('judul', 'Ganti Password')

@section('konten')
    <form method="POST" action="{{ route('password.update') }}" class="card max-w-lg space-y-4 p-6">
        @csrf
        @method('PUT')
        <x-field name="current_password" label="Password lama" type="password" required autocomplete="current-password" />
        <x-field name="password" label="Password baru" type="password" required autocomplete="new-password" hint="Minimal 8 karakter." />
        <x-field name="password_confirmation" label="Ulangi password baru" type="password" required autocomplete="new-password" />
        <div class="flex gap-2 pt-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
@endsection
