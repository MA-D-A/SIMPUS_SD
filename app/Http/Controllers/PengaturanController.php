<?php

namespace App\Http\Controllers;

use App\Http\Requests\PengaturanRequest;
use App\Models\Pengaturan;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PengaturanController extends Controller
{
    public function edit(): View
    {
        return view('pengaturan.edit', ['pengaturan' => Pengaturan::ambil()]);
    }

    public function update(PengaturanRequest $request): RedirectResponse
    {
        Pengaturan::ambil()->update($request->safe()->except('nama_petugas'));
        // Nama petugas = nama akun admin; dipakai di bilah atas dan kolom tanda tangan slip/laporan.
        $request->user()->update(['name' => $request->validated('nama_petugas')]);

        return redirect()->route('pengaturan.edit')->with('sukses', 'Pengaturan disimpan.');
    }
}
