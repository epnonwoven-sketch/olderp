#!/bin/bash
set -e

cd /var/www/html

php artisan config:clear
php artisan package:discover --ansi
php artisan storage:link || true

if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"
