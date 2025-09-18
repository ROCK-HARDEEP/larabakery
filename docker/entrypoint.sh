#!/usr/bin/env bash
set -e

# Go to app root
cd /var/www

# --- Make Nginx listen on Railway's $PORT ---
LISTEN_PORT="${PORT:-80}"
mkdir -p /opt/docker/etc/nginx/vhost.common.d
cat > /opt/docker/etc/nginx/vhost.common.d/00-listen.conf <<EOF
listen ${LISTEN_PORT};
listen [::]:${LISTEN_PORT};
EOF
# --------------------------------------------

# Generate APP_KEY if missing (idempotent)
if [ -z "${APP_KEY:-}" ]; then
  php artisan key:generate --force || true
fi

# Storage symlink (safe to re-run)
php artisan storage:link >/dev/null 2>&1 || true

# Cache config/routes (OK if providers touch DB)
php artisan config:cache || true
php artisan route:cache  || true

# NOTE: Temporarily skip view cache due to your Filament component error
# php artisan view:cache   || true

# Hand off to the base image entrypoint (will start supervisord -> nginx + php-fpm)
exec /opt/docker/bin/entrypoint.sh "$@"
