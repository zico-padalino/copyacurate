<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

if (is_dir('/tmp') && is_writable('/tmp')) {
    $storagePath = '/tmp/laravel-storage';

    foreach ([
        $storagePath.'/app/public',
        $storagePath.'/framework/cache/data',
        $storagePath.'/framework/sessions',
        $storagePath.'/framework/views',
        $storagePath.'/logs',
    ] as $directory) {
        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
    }

    $cachedViews = __DIR__.'/../storage/framework/views';
    if (is_dir($cachedViews)) {
        foreach (glob($cachedViews.'/*.php') ?: [] as $cachedView) {
            $target = $storagePath.'/framework/views/'.basename($cachedView);
            if (! is_file($target)) {
                @copy($cachedView, $target);
            }
        }
    }

    $app->useStoragePath($storagePath);
}

$app->handleRequest(Request::capture());
