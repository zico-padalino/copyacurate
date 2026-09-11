<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

header('Content-Type: text/plain; charset=utf-8');

error_reporting(E_ALL);
ini_set('display_errors', '1');

echo 'php='.PHP_VERSION.PHP_EOL;

try {
    require __DIR__.'/../vendor/autoload.php';

    $app = require __DIR__.'/../bootstrap/app.php';
    $app->make(ConsoleKernel::class)->bootstrap();

    echo 'boot=ok'.PHP_EOL;
    echo 'app_key_set='.(filled(config('app.key')) ? 'yes' : 'no').PHP_EOL;
    echo 'db='.config('database.default').PHP_EOL;
    echo 'sqlite='.config('database.connections.sqlite.database').PHP_EOL;
    echo 'session='.config('session.driver').PHP_EOL;
    echo 'cache='.config('cache.default').PHP_EOL;
    echo 'app_url='.config('app.url').PHP_EOL;

    DB::connection()->getPdo();
    echo 'db_pdo=ok'.PHP_EOL;
    echo 'users='.User::query()->count().PHP_EOL;

    $http = $app->make(HttpKernel::class);

    foreach (['/health', '/login'] as $path) {
        try {
            $response = $http->handle(Request::create($path, 'GET'));
            echo $path.'=status:'.$response->getStatusCode().PHP_EOL;
            if ($response->getStatusCode() >= 400) {
                $body = trim(preg_replace('/\s+/', ' ', strip_tags($response->getContent())) ?? '');
                echo $path.'=body:'.substr($body, 0, 240).PHP_EOL;
            }
        } catch (Throwable $exception) {
            echo $path.'=error:'.$exception::class.' '.$exception->getMessage().PHP_EOL;
            echo $path.'=file:'.$exception->getFile().':'.$exception->getLine().PHP_EOL;
        }
    }

    echo 'status=ok'.PHP_EOL;
} catch (Throwable $exception) {
    http_response_code(500);
    echo 'error='.$exception::class.PHP_EOL;
    echo 'message='.$exception->getMessage().PHP_EOL;
    echo 'file='.$exception->getFile().':'.$exception->getLine().PHP_EOL;
}
