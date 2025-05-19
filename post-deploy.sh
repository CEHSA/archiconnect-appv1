#!/bin/bash

# Post-deployment script for ArchiConnect App on cPanel
# Run this script after deploying the application to cPanel

echo "Running post-deployment tasks..."

# Copy production environment file to .env if it doesn't exist
if [ ! -f ".env" ]; then
    echo "Creating .env file from .env.production..."
    cp .env.production .env
fi

# Install composer dependencies
echo "Installing composer dependencies..."
composer install --no-dev --optimize-autoloader

# Generate application key if not set
if grep -q "APP_KEY=base64:" .env; then
    echo "Application key already set."
else
    echo "Generating application key..."
    php artisan key:generate
fi

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Cache configuration and routes
echo "Caching configuration and routes..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link
echo "Creating storage link..."
php artisan storage:link

# Set proper permissions
echo "Setting proper permissions..."
chmod -R 755 storage bootstrap/cache
find storage -type d -exec chmod 755 {} \;
find bootstrap/cache -type d -exec chmod 755 {} \;

echo "Post-deployment tasks completed!"
