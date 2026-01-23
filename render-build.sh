#!/bin/bash

# Exit on error
set -e

echo "Deploying SoloCart Backend..."

# Install dependencies
composer install --optimize-autoloader --no-dev

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Run Migrations
echo "Running migrations..."
php artisan migrate --force

# Link Storage
echo "Linking storage..."
php artisan storage:link

# Start Server
echo "Starting server..."
php artisan serve --host=0.0.0.0 --port=8080
