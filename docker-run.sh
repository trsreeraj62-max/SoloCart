#!/bin/bash

# Exit on error
set -e

echo "Running Deployment Script..."

# Handling dynamic PORT for Render
# Render sets the $PORT environment variable. Apache must listen on this port.
if [ -n "$PORT" ]; then
    echo "Configuring Apache to listen on port $PORT..."
    sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
    sed -i "s/:80/:${PORT}/" /etc/apache2/sites-available/000-default.conf
fi

# Link Storage
# Remove existing link/directory to avoid conflicts
echo "Refreshing storage link..."
rm -rf public/storage
php artisan storage:link

# Run Migrations
echo "Running migrations..."
php artisan migrate --force

# Run Seeder (Safe: uses firstOrCreate)
echo "Seeding default banners..."
php artisan db:seed --class=BannerSeeder --force
echo "Seeding admin user..."
php artisan db:seed --class=AdminSeeder --force

# Start Apache
echo "Starting Apache..."
exec apache2-foreground
