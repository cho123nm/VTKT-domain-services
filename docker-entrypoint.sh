#!/bin/bash
set -e

# Create symbolic link for assets if it doesn't exist
if [ ! -L /var/www/html/public/assets ] && [ ! -d /var/www/html/public/assets ]; then
    ln -s /var/www/html/assets /var/www/html/public/assets
    echo "Created symbolic link: /var/www/html/public/assets -> /var/www/html/assets"
fi

# Create symbolic link for Adminstators if it exists in root
if [ -d /var/www/html/Adminstators ] && [ ! -L /var/www/html/public/Adminstators ] && [ ! -d /var/www/html/public/Adminstators ]; then
    ln -s /var/www/html/Adminstators /var/www/html/public/Adminstators
    echo "Created symbolic link: /var/www/html/public/Adminstators -> /var/www/html/Adminstators"
fi

# Create symbolic link for images if it exists in root
if [ -d /var/www/html/images ] && [ ! -L /var/www/html/public/images ] && [ ! -d /var/www/html/public/images ]; then
    ln -s /var/www/html/images /var/www/html/public/images
    echo "Created symbolic link: /var/www/html/public/images -> /var/www/html/images"
fi

# Create images/admin directory if it doesn't exist
if [ ! -d /var/www/html/images/admin ]; then
    mkdir -p /var/www/html/images/admin
    chown -R www-data:www-data /var/www/html/images/admin
    echo "Created directory: /var/www/html/images/admin"
fi

# Set proper permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Execute the main command
exec "$@"

