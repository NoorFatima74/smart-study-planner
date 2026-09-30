#!/bin/sh

set -e

# ---------------------------------------------------------
# Create Aiven CA certificate
# ---------------------------------------------------------
if [ -n "${AIVEN_CA_BASE64:-}" ]; then
    echo "$AIVEN_CA_BASE64" | base64 -d > /data/aiven-ca.pem
    chmod 644 /data/aiven-ca.pem
fi


# ---------------------------------------------------------
# Prepare persistent Laravel storage
# ---------------------------------------------------------
mkdir -p \
    /data/storage/app/public \
    /data/storage/framework/cache \
    /data/storage/framework/sessions \
    /data/storage/framework/views \
    /data/storage/logs


# ---------------------------------------------------------
# Start Apache
# ---------------------------------------------------------
exec apache2-foreground