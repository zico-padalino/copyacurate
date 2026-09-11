<?php

declare(strict_types=1);
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

header('Content-Type: text/plain; charset=utf-8');

error_reporting(E_ALL);
ini_set('display_errors', '1');

echo 'php='.PHP_VERSION.PHP_EOL;

try {
    require __DIR__.'/../vendor/autoload.php';

    $app = require __DIR__.'/../bootstrap/app.php';
    $app->make(Kernel::class)->bootstrap();

    echo 'boot=ok'.PHP_EOL;
    echo 'app_key_set='.(filled(config('app.key')) ? 'yes' : 'no').PHP_EOL;
    echo 'db='.config('database.default').PHP_EOL;
    echo 'sqlite='.config('database.connections.sqlite.database').PHP_EOL;
    echo 'session='.config('session.driver').PHP_EOL;
    echo 'cache='.config('cache.default').PHP_EOL;

    DB::connection()->getPdo();
    echo 'db_pdo=ok'.PHP_EOL;
    echo 'users='.User::query()->count().PHP_EOL;
    echo 'status=ok'.PHP_EOL;
} catch (Throwable $exception) {
    http_response_code(500);
    echo 'error='.$exception::class.PHP_EOL;
    echo 'message='.$exception->getMessage().PHP_EOL;
    echo 'file='.$exception->getFile().':'.$exception->getLine().PHP_EOL;
}
