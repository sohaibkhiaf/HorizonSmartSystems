# ==============================================================================
# Base Stage: Ubuntu 24.04 with Apache & PHP 8.3
# ==============================================================================
FROM ubuntu:24.04 AS base

ENV DEBIAN_FRONTEND=noninteractive \
    TZ=Africa/Algiers \
    APACHE_DOCUMENT_ROOT=/var/www/html/public

# Install system dependencies, Apache, PHP, and required PHP extensions for Laravel
RUN apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates \
    curl \
    unzip \
    git \
    zip \
    apache2 \
    libapache2-mod-php \
    php \
    php-cli \
    php-common \
    php-mysql \
    php-xml \
    php-mbstring \
    php-curl \
    php-zip \
    php-gd \
    php-bcmath \
    php-intl \
    php-sqlite3 \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configure Apache DocumentRoot to /var/www/html/public and enable mod_rewrite
RUN a2enmod rewrite headers \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}/!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

WORKDIR /var/www/html

# Create entrypoint script
RUN printf '%s\n' \
    '#!/usr/bin/env bash' \
    'set -e' \
    'mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache public/uploads' \
    'chown -R www-data:www-data storage bootstrap/cache public/uploads || true' \
    'chmod -R 775 storage bootstrap/cache public/uploads || true' \
    'if [ "$APP_ENV" = "local" ] && [ ! -f "vendor/autoload.php" ]; then' \
    '    composer install --no-interaction --prefer-dist' \
    'fi' \
    'exec "$@"' \
    > /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["apache2ctl", "-D", "FOREGROUND"]

# ==============================================================================
# Development Stage
# ==============================================================================
FROM base AS dev

ENV APP_ENV=local \
    APP_DEBUG=true

COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --no-scripts

COPY . .

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/uploads \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/uploads

# ==============================================================================
# Production Stage
# ==============================================================================
FROM base AS prod

ENV APP_ENV=production \
    APP_DEBUG=false

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

COPY . .

RUN composer dump-autoload --optimize --no-dev \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/uploads \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/uploads
