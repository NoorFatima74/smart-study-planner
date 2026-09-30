#!/bin/sh

set -e

PORT="${PORT:-3000}"

# Configure Apache to use Deplexo's runtime port
sed -i "s/^Listen 80$/Listen ${PORT}/" /etc/apache2/ports.conf

sed -i \
    "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" \
    /etc/apache2/sites-available/000-default.conf


# Create Aiven CA certificate from the runtime environment variable
if [ -n "${AIVEN_CA_BASE64:-}" ]; then
    echo "$AIVEN_CA_BASE64" | base64 -d > /data/aiven-ca.pem
    chmod 644 /data/aiven-ca.pem
fi


# Persistent Laravel storage
mkdir -p \
    /data/storage/app/public \
    /data/storage/framework/cache \
    /data/storage/framework/sessions \
    /data/storage/framework/views \
    /data/storage/logs

chown -R www-data:www-data /data/storage


# Start Apache
exec apache2-foreground