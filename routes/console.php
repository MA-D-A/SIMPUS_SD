<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;

Artisan::command('admin:reset-password', function () {
    $admin = User::first();

    if (! $admin) {
        $this->error('Akun admin belum ada. Jalankan: php artisan migrate --seed');

        return 1;
    }

    $password = (string) $this->secret('Password baru (minimal 8 karakter)');

    if (mb_strlen($password) < 8) {
        $this->error('Password minimal 8 karakter.');

        return 1;
    }

    if ($password !== $this->secret('Ulangi password baru')) {
        $this->error('Password tidak sama.');

        return 1;
    }

    $admin->update(['password' => $password]);
    $this->info("Password admin ({$admin->username}) berhasil direset.");

    return 0;
})->purpose('Reset password akun admin jika lupa');
