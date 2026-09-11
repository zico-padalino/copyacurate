#!/bin/sh
set -e

cd /var/www/html

if [ ! -d vendor ]; then
    composer install --no-interaction --prefer-dist
fi

if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

if [ -f .env ] && ! grep -qE '^APP_KEY=.+' .env; then
    php artisan key:generate --force --no-interaction || true
fi

chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

exec docker-php-entrypoint "$@"
