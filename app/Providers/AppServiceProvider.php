<?php

namespace App\Providers;

use App\Models\Pengaturan;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Identitas sekolah dipakai di bilah atas, halaman login, dan kop cetak.
        View::composer(['layouts.app', 'layouts.cetak', 'laporan.cetak', 'auth.login'], function ($view) {
            $view->with('pengaturan', Pengaturan::ambil());
        });
    }
}
