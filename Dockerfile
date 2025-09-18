# Nginx + PHP-FPM in one image (Debian, maintained)
FROM webdevops/php-nginx:8.3

# Set doc root to Laravel public
ENV WEB_DOCUMENT_ROOT=/var/www/public
WORKDIR /var/www

# System libs for common Laravel stacks (MySQL, GD, Zip, Intl, Exif, Opcache, BCMath)
RUN apt-get update && apt-get install -y \
    libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libicu-dev libonig-dev \
    libxml2-dev zip unzip git curl \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j"$(nproc)" pdo_mysql gd intl zip exif bcmath opcache \
 && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy app (use --chown to avoid permission headaches)
COPY --chown=application:application . .

# Install PHP deps (no update in prod)
RUN composer install --no-dev --prefer-dist --no-progress --no-interaction --optimize-autoloader

# Cache PHP OPcache a bit (optional but good)
RUN echo "opcache.enable=1\nopcache.enable_cli=1\nopcache.validate_timestamps=0\nopcache.max_accelerated_files=20000\nopcache.memory_consumption=192\nopcache.interned_strings_buffer=16" \
    > /usr/local/etc/php/conf.d/opcache.ini

# Ensure storage/bootstrap writable
RUN mkdir -p storage/framework/{cache,sessions,views} \
 && chown -R application:application storage bootstrap/cache

# Add entrypoint to run Laravel commands at runtime (after env is available)
COPY ./docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Railway sets $PORT; nginx in this image listens on 80 by default.
# We proxy 0.0.0.0:80 -> php-fpm automatically (handled by the base image).
EXPOSE 80

# Use our entrypoint, then start the base supervisord (nginx+php-fpm)
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/opt/docker/bin/entrypoint", "supervisord"]
