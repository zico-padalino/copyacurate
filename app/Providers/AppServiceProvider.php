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
        // Vercel often imports blank env values (""), which break Laravel managers.
        config([
            'app.maintenance.driver' => filled(config('app.maintenance.driver')) ? config('app.maintenance.driver') : 'file',
            'app.maintenance.store' => filled(config('app.maintenance.store')) ? config('app.maintenance.store') : 'array',
            'session.driver' => filled(config('session.driver')) ? config('session.driver') : 'cookie',
            'cache.default' => filled(config('cache.default')) ? config('cache.default') : 'array',
            'queue.default' => filled(config('queue.default')) ? config('queue.default') : 'sync',
            'filesystems.default' => filled(config('filesystems.default')) ? config('filesystems.default') : 'local',
            'mail.default' => filled(config('mail.default')) ? config('mail.default') : 'log',
            'logging.default' => filled(config('logging.default')) ? config('logging.default') : 'stderr',
        ]);

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
