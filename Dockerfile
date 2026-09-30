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

# Copy Vite production assets
COPY --from=frontend /app/public/build ./public/build


# ---------------------------------------------------------
# Install production PHP dependencies
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
    '<VirtualHost *:80>' \
    '    DocumentRoot /var/www/html/public' \
    '    <Directory /var/www/html/public>' \
    '        AllowOverride All' \
    '        Require all granted' \
    '    </Directory>' \
    '</VirtualHost>' \
    > /etc/apache2/sites-available/000-default.conf


# ---------------------------------------------------------
# Prepare Laravel storage
#
# Deplexo provides persistent writable storage at /data.
# Laravel's storage directory is linked there.
# ---------------------------------------------------------
RUN rm -rf /var/www/html/storage \
    && ln -s /data/storage /var/www/html/storage


# Public storage link
RUN rm -f /var/www/html/public/storage \
    && ln -s /data/storage/app/public /var/www/html/public/storage


# ---------------------------------------------------------
# Runtime entrypoint
# ---------------------------------------------------------
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh

RUN chmod +x /usr/local/bin/docker-entrypoint.sh


# Apache listens on the PORT supplied by Deplexo
EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]