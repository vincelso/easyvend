#!/bin/sh

# Clear old caches first
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link

# Run migrations
php artisan migrate --force

# Run seeders
php artisan db:seed --force

# Start PHP-FPM in background
php-fpm -D

# Start Nginx in foreground
nginx -g "daemon off;"