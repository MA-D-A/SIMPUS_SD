@extends('layouts.app')

@section('judul', 'Anggota')

@section('aksi')
    <a href="{{ route('anggota.import') }}" class="btn btn-secondary">Import Excel</a>
    <a href="{{ route('anggota.create') }}" class="btn btn-primary">+ Tambah Anggota</a>
@endsection

@section('konten')
    @php($adaFilter = request()->anyFilled(['cari', 'jenis', 'kelas', 'status']))

    <form method="GET" role="search" class="mb-4 flex flex-wrap items-end gap-3">
        <div class="w-full sm:w-72">
            <x-field name="cari" label="Cari" type="search" :value="request('cari')" placeholder="Nama atau nomor induk" />
        </div>
        <div class="w-full sm:w-36">
            <label for="jenis" class="block text-sm font-medium text-slate-700">Jenis</label>
            <select id="jenis" name="jenis" class="input">
                <option value="">Semua</option>
                <option value="siswa" @selected(request('jenis') === 'siswa')>Siswa</option>
                <option value="guru" @selected(request('jenis') === 'guru')>Guru</option>
            </select>
        </div>
        <div class="w-full sm:w-32">
            <label for="kelas" class="block text-sm font-medium text-slate-700">Kelas</label>
            <select id="kelas" name="kelas" class="input">
                <option value="">Semua</option>
                @foreach ($semuaKelas as $kelas)
                    <option value="{{ $kelas }}" @selected(request('kelas') === $kelas)>{{ $kelas }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full sm:w-36">
            <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
            <select id="status" name="status" class="input">
                <option value="">Semua</option>
                <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
            </select>
        </div>
        <button type="submit" class="btn btn-secondary">Cari</button>
        @if ($adaFilter)
            <a href="{{ route('anggota.index') }}" class="link py-2">Hapus filter</a>
        @endif
    </form>

    <div class="card overflow-x-auto">
        <table class="tabel">
            <thead>
                <tr>
                    <th>Nomor induk</th>
                    <th>Nama</th>
                    <th>Jenis</th>
                    <th>Kelas</th>
                    <th>Status</th>
                    <th><span class="sr-only">Aksi</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($anggota as $a)
                    <tr>
                        <td class="font-mono text-xs whitespace-nowrap">{{ $a->nomor_induk }}</td>
                        <td><a href="{{ route('anggota.show', $a) }}" class="link">{{ $a->nama }}</a></td>
                        <td>{{ ucfirst($a->jenis) }}</td>
                        <td>{{ $a->kelas ?? '–' }}</td>
                        <td><x-badge-status :status="$a->aktif ? 'aktif' : 'nonaktif'" /></td>
                        <td class="text-right whitespace-nowrap">
                            <a href="{{ route('anggota.edit', $a) }}" class="link">Ubah</a>
                            <x-tombol-hapus link class="ml-4" :action="route('anggota.destroy', $a)" :konfirmasi="'Hapus anggota '.$a->nama.'?'" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-500">
                            {{ $adaFilter ? 'Anggota tidak ditemukan.' : 'Belum ada data anggota.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $anggota->links() }}</div>
@endsection
