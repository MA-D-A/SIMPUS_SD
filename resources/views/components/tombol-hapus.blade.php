{{-- Tombol hapus/batal dengan konfirmasi (lihat resources/js/app.js). Pakai atribut `link` di dalam tabel. --}}
@props(['action', 'konfirmasi' => 'Hapus data ini?', 'label' => 'Hapus', 'link' => false])

<form method="POST" action="{{ $action }}" data-konfirmasi="{{ $konfirmasi }}" class="inline">
    @csrf
    @method('DELETE')
    <button type="submit" {{ $attributes->class([$link ? 'link text-red-700' : 'btn btn-danger']) }}>{{ $label }}</button>
</form>

