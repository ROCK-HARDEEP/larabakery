#!/usr/bin/env bash
set -e

# Switch to app root
cd /var/www

# If APP_KEY is empty, generate one
if [ -z "${APP_KEY:-}" ]; then
  php artisan key:generate --force || true
fi

# Try to link storage (safe to re-run)
php artisan storage:link >/dev/null 2>&1 || true

# Cache configs/routes/views (ignore if DB not ready yet)
php artisan config:cache  || true
php artisan route:cache   || true
php artisan view:cache    || true

# Hand over to the base image’s process (nginx + php-fpm)
exec "$@"
