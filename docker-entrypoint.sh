#!/bin/sh

set -e

# Deplexo supplies PORT at runtime.
PORT="${PORT:-80}"

# Configure Apache to use Deplexo's port.
sed -i "s/^Listen 80$/Listen ${PORT}/" /etc/apache2/ports.conf

sed -i \
    "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" \
    /etc/apache2/sites-available/000-default.conf


# ---------------------------------------------------------
# Prepare persistent Laravel storage
# ---------------------------------------------------------
mkdir -p \
    /data/storage/app/public \
    /data/storage/framework/cache \
    /data/storage/framework/sessions \
    /data/storage/framework/views \
    /data/storage/logs


# Make Laravel storage writable by Apache.
chown -R www-data:www-data /data/storage


# ---------------------------------------------------------
# Start Apache
# ---------------------------------------------------------
exec apache2-foreground