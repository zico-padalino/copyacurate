#!/bin/sh
set -eu

# Ignore leftover Vercel/local env that breaks the demo image.
export DB_CONNECTION=sqlite
cp -f /app/database/database.sqlite /tmp/database.sqlite
chmod 666 /tmp/database.sqlite
export DB_DATABASE=/tmp/database.sqlite
unset DB_HOST DB_PORT DB_USERNAME DB_PASSWORD DB_SOCKET DB_URL DATABASE_URL MYSQL_ATTR_SSL_CA || true

# Always use the baked demo key unless a real base64 key was provided.
case "${APP_KEY:-}" in
    base64:*) ;;
    *) export APP_KEY=base64:jhy2XeXMVioryYjMHhGf8jPtWmiHCrfJG2BzhBet3Rw= ;;
esac

export APP_ENV=production
export APP_DEBUG=false
export CACHE_STORE=array
export SESSION_DRIVER=cookie
export QUEUE_CONNECTION=sync
export LOG_CHANNEL=stderr
export LOG_STACK=single

if [ -n "${VERCEL_URL:-}" ]; then
    export APP_URL="https://${VERCEL_URL}"
else
    export APP_URL="${APP_URL:-https://kabarbanten-keuangan.vercel.app}"
fi

exec frankenphp run --config /etc/frankenphp/Caddyfile
