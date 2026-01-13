#!/usr/bin/env bash

# Exit on error
set -e

echo "🚀 Starting SoloCart Build Protocol..."

# Install dependencies
composer install --no-dev --optimize-autoloader

# Run database migrations
php artisan migrate --force

# Clear and optimize caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize

# Create storage link if it doesn't exist
php artisan storage:link || true

echo "✅ Build Sequence Complete. Manifest Ready."
