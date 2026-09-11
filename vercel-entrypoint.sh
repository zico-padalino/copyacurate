#!/bin/sh
set -eu

# Writable scratch space (Vercel container FS is effectively read-only).
export DB_CONNECTION=sqlite
cp -f /app/database/database.sqlite /tmp/database.sqlite
chmod 666 /tmp/database.sqlite
export DB_DATABASE=/tmp/database.sqlite
unset DB_HOST DB_PORT DB_USERNAME DB_PASSWORD DB_SOCKET DB_URL DATABASE_URL MYSQL_ATTR_SSL_CA || true

mkdir -p \
    /tmp/laravel-storage/app/public \
    /tmp/laravel-storage/framework/cache/data \
    /tmp/laravel-storage/framework/sessions \
    /tmp/laravel-storage/framework/views \
    /tmp/laravel-storage/logs

if [ -d /app/storage/framework/views ]; then
    cp -a /app/storage/framework/views/. /tmp/laravel-storage/framework/views/ 2>/dev/null || true
fi

export VIEW_COMPILED_PATH=/tmp/laravel-storage/framework/views

# Always use the baked demo key (ignore invalid/empty Vercel overrides).
export APP_KEY=base64:jhy2XeXMVioryYjMHhGf8jPtWmiHCrfJG2BzhBet3Rw=

export APP_ENV=production
export APP_DEBUG=true
export CACHE_STORE=array
export SESSION_DRIVER=cookie
export SESSION_ENCRYPT=false
unset SESSION_DOMAIN || true
export QUEUE_CONNECTION=sync
export LOG_CHANNEL=stderr
export LOG_STACK=single

if [ -n "${VERCEL_URL:-}" ]; then
    export APP_URL="https://${VERCEL_URL}"
else
    export APP_URL="${APP_URL:-https://kabarbanten-keuangan.vercel.app}"
fi

exec frankenphp run --config /etc/frankenphp/Caddyfile
