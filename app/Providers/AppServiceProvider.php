<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver;
use App\Models\RawatInap\RawatInap;
use App\Observers\RawatInapObserver;
use Illuminate\Support\ServiceProvider;
use App\Observers\DetailPembayaranPasienObserver;
use App\Models\DetailPembayaranPasien\DetailPembayaranPasien;

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
        //  User::observe(UserObserver::class);
        RawatInap::observe(RawatInapObserver::class);
        DetailPembayaranPasien::observe(DetailPembayaranPasienObserver::class);
    }
}
