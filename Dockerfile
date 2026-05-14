# ──────────────────────────────────────────────────────────────────────
# VisionLab— Laravel Application Dockerfile
# ──────────────────────────────────────────────────────────────────────
# Multi-stage build: PHP 8.3 FPM with all required extensions.
# ──────────────────────────────────────────────────────────────────────

# ── Stage 1: Build assets ──────────────────────────────────────────
FROM node:20-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources/ resources/
RUN npm run build

# ── Stage 2: Composer dependencies ──────────────────────────────────
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# ── Stage 3: Final image ───────────────────────────────────────────
FROM php:8.3-fpm-alpine

# Install required PHP extensions
RUN apk add --no-cache \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    libzip-dev oniguruma-dev \
    icu-dev linux-headers \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo_mysql mbstring gd zip exif pcntl bcmath intl opcache sockets

# Install Redis extension
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# PHP production config
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/zzz-visioncode.conf

WORKDIR /var/www/html

# Copy application
COPY --from=vendor /app/vendor ./vendor
COPY . .
COPY --from=assets /app/public/build ./public/build

# Generate autoloader and cache
RUN composer dump-autoload --optimize --no-dev \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Storage permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
