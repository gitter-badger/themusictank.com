<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Logging\Log;
use Psr\Log\LoggerInterface;

use App\Models\User;
use App\Observers\UserObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(UserObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (getenv('APP_ENV') === "production") {
            $this->app->alias('bugsnag.logger', Log::class);
            $this->app->alias('bugsnag.logger', LoggerInterface::class);
        }
    }
}
