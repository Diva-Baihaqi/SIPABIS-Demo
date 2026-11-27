# syntax=docker/dockerfile:1

# ==============================================================================
# Stage 1: Base PHP 8.4 + Apache Runtime
# ==============================================================================
FROM php:8.4-apache AS php-base

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libwebp-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    curl \
    && docker-php-ext-configure gd --with-webp --with-jpeg --with-freetype \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql gd zip bcmath opcache \
    && a2enmod rewrite headers \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && sed -ri -e 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf \
    && rm -rf /var/lib/apt/lists/*

# ==============================================================================
# Stage 2: Build Frontend Assets (Tailwind CSS + Alpine.js via Vite)
# ==============================================================================
FROM node:22-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY resources/ resources/
COPY tailwind.config.js postcss.config.js vite.config.js ./
RUN npm run build

# ==============================================================================
# Stage 3: Install PHP Dependencies (PHP 8.4 + Composer --no-dev)
# ==============================================================================
FROM php-base AS vendor

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

# ==============================================================================
# Stage 4: Production Image
# ==============================================================================
FROM php-base AS production

LABEL org.opencontainers.image.source="https://github.com/Diva-Baihaqi/SIPABIS-Demo"
LABEL org.opencontainers.image.title="SIPABIS Demo (Portfolio Edition)"
LABEL org.opencontainers.image.description="Demo Portofolio Sistem Informasi Persediaan Barang Pakai Habis (SIPABIS) berbasis Laravel 11 dengan data simulasi."
LABEL org.opencontainers.image.licenses="MIT"

WORKDIR /var/www/html

COPY . /var/www/html
COPY --from=vendor /app/vendor /var/www/html/vendor
COPY --from=assets /app/public/build /var/www/html/public/build

RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache \
    && php artisan package:discover --ansi \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD curl -fsS http://127.0.0.1/up || exit 1
