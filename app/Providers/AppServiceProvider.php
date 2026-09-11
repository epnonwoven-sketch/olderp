<?php

namespace App\Providers;

use App\Models\BagPacking;
use App\Models\OrderPunchDetail;
use App\Models\RollDetail;
use App\Models\RollTransit;
use App\Observers\BagPackingObserver;
use App\Observers\OrderPunchDetailObserver;
use App\Observers\RollDetailObserver;
use App\Observers\RollTransitObserver;
use Illuminate\Support\Facades\URL;
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
        if ($this->app->environment('production') || request()->header('x-forwarded-proto') === 'https' || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
            URL::forceScheme('https');
        }

        RollTransit::observe(RollTransitObserver::class);
        RollDetail::observe(RollDetailObserver::class);
        OrderPunchDetail::observe(OrderPunchDetailObserver::class);
        BagPacking::observe(BagPackingObserver::class);

    }
}
