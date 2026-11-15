@extends('layouts.app')

@section('judul', 'Import Buku')

@section('aksi')
    <a href="{{ route('buku.index') }}" class="btn btn-secondary">Kembali ke Buku</a>
@endsection

@section('konten')
    <div class="max-w-xl space-y-6">
        <div class="card p-6">
            <div class="mb-5">
                <h2 class="text-base font-semibold text-slate-900">Unggah file</h2>
                <p class="mt-1 text-sm text-slate-600">
                    Pilih file Excel (.xlsx) atau CSV. Baris pertama harus berisi header kolom.
                </p>
            </div>

            <form method="POST" action="{{ route('buku.import.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <x-field name="file" label="File Excel / CSV" type="file" required
                    hint="kode_buku boleh kosong (otomatis jadi BK-0001 dst). Unduh template untuk format kolom yang benar." />

                <div class="flex flex-wrap items-center gap-3 border-t border-slate-100 pt-5">
                    <button type="submit" class="btn btn-primary">Import</button>
                    <a href="{{ route('buku.import.template') }}" class="btn btn-secondary">Unduh template</a>
                </div>
            </form>
        </div>

        @if (session('hasil'))
            @php($hasil = session('hasil'))
            <div class="card p-6">
                <p class="font-medium">
                    {{ $hasil['berhasil'] }} berhasil, {{ count($hasil['gagal']) }} gagal.
                </p>

                @if ($hasil['gagal'] !== [])
                    <ul class="mt-3 space-y-1 text-sm text-red-700">
                        @foreach ($hasil['gagal'] as $pesan)
                            <li>{{ $pesan }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif
    </div>
@endsection
