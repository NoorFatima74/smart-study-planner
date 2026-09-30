# =========================================================
# Stage 1: Build Laravel Vite assets
# =========================================================
FROM node:24-alpine AS frontend

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build


# =========================================================
# Stage 2: Laravel application
# =========================================================
FROM php:8.2-apache

# ---------------------------------------------------------
# System packages + PHP extensions
# ---------------------------------------------------------
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        bcmath \
        intl \
        opcache \
        zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*


# ---------------------------------------------------------
# Composer
# ---------------------------------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


# ---------------------------------------------------------
# Laravel application
# ---------------------------------------------------------
WORKDIR /var/www/html

COPY . .

COPY --from=frontend /app/public/build ./public/build


# ---------------------------------------------------------
# Install production dependencies
# ---------------------------------------------------------
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader


# ---------------------------------------------------------
# Apache configuration
# ---------------------------------------------------------
RUN printf '%s\n' \
    '<VirtualHost *:3000>' \
    '    DocumentRoot /var/www/html/public' \
    '    <Directory /var/www/html/public>' \
    '        AllowOverride All' \
    '        Require all granted' \
    '    </Directory>' \
    '</VirtualHost>' \
    > /etc/apache2/sites-available/000-default.conf \
    && sed -i 's/^Listen 80$/Listen 3000/' /etc/apache2/ports.conf


# ---------------------------------------------------------
# Persistent Laravel storage
# ---------------------------------------------------------
RUN rm -rf /var/www/html/storage \
    && mkdir -p /data/storage \
    && ln -s /data/storage /var/www/html/storage

RUN rm -f /var/www/html/public/storage \
    && mkdir -p /data/storage/app/public \
    && ln -s /data/storage/app/public /var/www/html/public/storage


# ---------------------------------------------------------
# Runtime entrypoint
# ---------------------------------------------------------
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh

RUN chmod +x /usr/local/bin/docker-entrypoint.sh


EXPOSE 3000

ENTRYPOINT ["docker-entrypoint.sh"]