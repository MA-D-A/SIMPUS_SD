@if (session('sukses'))
    <div role="status" class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
        {{ session('sukses') }}
    </div>
@endif

@if (session('gagal'))
    <div role="alert" class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
        {{ session('gagal') }}
    </div>
@endif

@if ($errors->any())
    <div role="alert" class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
        Ada {{ $errors->count() }} isian yang perlu diperbaiki. Lihat pesan di bawah kolom yang ditandai.
    </div>
@endif
