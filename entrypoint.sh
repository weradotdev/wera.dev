#!/bin/sh

set -e

echo "Fixing storage and cache permissions..."
chown -R unit:unit /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "Storage directories:"
ls -la /var/www/html/storage
ls -la /var/www/html/bootstrap/cache

php artisan optimize:clear

# Link storage and public folders
php artisan storage:link || true

php artisan migrate --force

php artisan db:seed --force

echo "Done."

exec "$@"
