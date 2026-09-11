<?php

namespace App\Providers;

use App\Support\AppSettings;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        View::composer(['layouts.app', 'layouts.guest', 'partials.sidebar', 'auth.login'], function ($view): void {
            $view->with([
                'appName' => AppSettings::appName(),
                'companyName' => AppSettings::companyName(),
                'tagline' => AppSettings::tagline(),
            ]);
        });
    }
}
