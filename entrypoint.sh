#!/bin/sh

set -e

echo "Fixing storage and cache permissions..."
chown -R unit:unit /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Link storage and public folders
php artisan storage:link || true

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "Waiting for database..."
    until php artisan db:monitor --max=1 > /dev/null 2>&1; do
        sleep 2
    done
    echo "Database is ready."

    php artisan migrate --force
    php artisan db:seed --force || true
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Done."

exec unitd --no-daemon
