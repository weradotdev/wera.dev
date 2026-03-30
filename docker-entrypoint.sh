#!/bin/sh
# Fix permissions for Laravel storage and cache at runtime
chown -R unit:unit /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
exec "$@"
