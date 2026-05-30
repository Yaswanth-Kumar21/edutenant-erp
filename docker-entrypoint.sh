#!/bin/bash
set -e

cd /var/www/html

# Create .env from example if not exists
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Write all environment variables into .env
# This overwrites any existing values with what Render provides
cat > .env << EOF
APP_NAME="${APP_NAME:-EduTenant ERP}"
APP_ENV="${APP_ENV:-production}"
APP_KEY="${APP_KEY}"
APP_DEBUG="${APP_DEBUG:-false}"
APP_URL="${APP_URL:-http://localhost}"
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US
APP_MAINTENANCE_DRIVER=file
BCRYPT_ROUNDS=12
LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=error
DB_CONNECTION="${DB_CONNECTION:-mysql}"
DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE:-edutenant_erp}"
DB_USERNAME="${DB_USERNAME:-root}"
DB_PASSWORD="${DB_PASSWORD}"
MYSQL_ATTR_SSL_CA="${MYSQL_ATTR_SSL_CA}"
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=database
MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@edutenant.com"
MAIL_FROM_NAME="EduTenant ERP"
VITE_APP_NAME="EduTenant ERP"
RAZORPAY_KEY="${RAZORPAY_KEY}"
RAZORPAY_SECRET="${RAZORPAY_SECRET}"
EOF

# Run migrations
php artisan migrate --force

# Seed demo data (only if tables are empty) - skip on failure so app still starts
php artisan db:seed --force || echo "Seeding failed - app will start without demo data"

# Create storage symlink
php artisan storage:link || true

# Cache for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Apache
apache2-foreground
