{{-- Label status dengan warna tetap; teksnya selalu tampil supaya tidak bergantung pada warna saja. --}}
@props(['status'])

@php
    $warna = [
        'dipinjam' => 'bg-blue-50 text-blue-800 ring-blue-200',
        'terlambat' => 'bg-red-50 text-red-800 ring-red-200',
        'selesai' => 'bg-green-50 text-green-800 ring-green-200',
        'aktif' => 'bg-green-50 text-green-800 ring-green-200',
        'nonaktif' => 'bg-slate-100 text-slate-700 ring-slate-200',
    ][$status];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold whitespace-nowrap ring-1 ring-inset $warna"]) }}>
    {{ ucfirst($status) }}
</span>
