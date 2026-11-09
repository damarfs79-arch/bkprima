#!/bin/bash
set -e

if [ ! -f /var/www/.env ]; then
    if [ -f /var/www/.env.example ]; then
        echo "[Docker Entrypoint] Menyalin .env dari .env.example..."
        cp /var/www/.env.example /var/www/.env
    fi
fi

mkdir -p /var/www/storage/framework/cache/data \
         /var/www/storage/framework/sessions \
         /var/www/storage/framework/views \
         /var/www/storage/logs \
         /var/www/bootstrap/cache

chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true

if [ -f /var/www/.env ]; then
    if ! grep -q "^APP_KEY=base64:" /var/www/.env; then
        echo "[Docker Entrypoint] Generating Application Key..."
        php artisan key:generate --force || true
    fi
fi

exec "$@"
