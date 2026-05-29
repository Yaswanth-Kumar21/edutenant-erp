#!/bin/bash
set -e

# Copy .env if not exists
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
else
    echo "APP_KEY=$APP_KEY" >> .env
fi

# Write all env vars to .env file
printenv | grep -E "^(APP_|DB_|MAIL_|QUEUE_|CACHE_|SESSION_|FILESYSTEM_|RAZORPAY_)" >> .env

# Run migrations and seed
php artisan migrate --force
php artisan db:seed --force

# Create storage symlink
php artisan storage:link || true

# Cache config/routes/views
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Apache
apache2-foreground
