#!/bin/sh

# Cache Laravel config
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link

# Run migrations
php artisan migrate --force

# Start PHP-FPM in background
php-fpm -D

# Start Nginx in foreground
nginx -g "daemon off;"