{{--
    Kartu statistik: label, angka, keterangan. Angka tetap warna teks biasa;
    status kritis ditandai titik merah di samping label (tidak hanya warna).
--}}
@props(['label', 'nilai', 'keterangan' => null, 'href' => null, 'kritis' => false])

<a href="{{ $href }}" class="card block p-4 transition-colors duration-150 hover:border-primary/40 hover:bg-primary-soft/40">
    <p class="flex items-center gap-2 text-sm font-medium text-slate-600">
        @if ($kritis)
            <span class="size-2 shrink-0 rounded-full bg-red-600" aria-hidden="true"></span>
        @endif
        {{ $label }}
    </p>
    <p class="mt-2 text-3xl font-semibold text-slate-900">{{ number_format($nilai, 0, ',', '.') }}</p>
    @if ($keterangan)
        <p class="mt-1 text-sm text-slate-500">{{ $keterangan }}</p>
    @endif
</a>
