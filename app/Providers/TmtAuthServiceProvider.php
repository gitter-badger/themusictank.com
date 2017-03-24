<?php

namespace App\Providers;

use App\Models\ApiSessionToken;
use App\Models\Profiles;
use App\Auth\ApiUserProvider;
use App\Auth\User;
use Illuminate\Support\ServiceProvider;

class TmtAuthServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        auth()->provider("TmtAuth", function ($app, array $config) {
            return new ApiUserProvider(ApiSessionToken::api(), Profiles::api(), User::class);
        });
    }
}
