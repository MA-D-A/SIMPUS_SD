{{--
    Kolom form: label, input, petunjuk, dan pesan error yang terhubung lewat aria-describedby.
    Isi slot untuk kontrol selain <input> (misalnya <select>); kontrol itu harus memakai id yang sama.
--}}
@props(['name', 'label', 'type' => 'text', 'value' => null, 'required' => false, 'hint' => null])

@php
    $id = $attributes->get('id', $name);
    $keterangan = collect([$hint ? "$id-hint" : null, $errors->has($name) ? "$id-error" : null])->filter()->implode(' ');
@endphp

<div>
    <label for="{{ $id }}" class="block text-sm font-medium text-slate-700">
        {{ $label }}
        @if ($required)
            <span class="text-red-700" aria-hidden="true">*</span>
        @endif
    </label>

    @if ($slot->isEmpty())
        <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}"
               value="{{ $type === 'password' ? '' : old($name, $value) }}"
               @required($required)
               @if ($keterangan) aria-describedby="{{ $keterangan }}" @endif
               @error($name) aria-invalid="true" @enderror
               {{ $attributes->except('id')->merge(['class' => 'input']) }}>
    @else
        {{ $slot }}
    @endif

    @if ($hint)
        <p id="{{ $id }}-hint" class="mt-1 text-sm text-slate-500">{{ $hint }}</p>
    @endif

    @error($name)
        <p id="{{ $id }}-error" class="mt-1 text-sm text-red-700">{{ $message }}</p>
    @enderror
</div>
