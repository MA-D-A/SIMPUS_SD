<div class="card mb-4 p-4">
    <p class="text-sm text-slate-600">{{ $keterangan }}</p>
    <dl class="mt-3 flex flex-wrap gap-x-10 gap-y-3">
        @foreach ($rekap as $label => $nilai)
            <div>
                <dt class="text-sm text-slate-500">{{ $label }}</dt>
                <dd class="text-xl font-semibold">{{ number_format($nilai, 0, ',', '.') }}</dd>
            </div>
        @endforeach
    </dl>
</div>
