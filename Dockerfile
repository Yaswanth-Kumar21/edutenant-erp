# ────────────────────────────────────────────────────────────────────────────
# Stage 1 — Node: compile frontend assets
# ────────────────────────────────────────────────────────────────────────────
FROM node:20-alpine AS node-builder

WORKDIR /app
COPY package*.json ./
RUN npm ci --ignore-scripts
COPY . .
RUN npm run build

# ────────────────────────────────────────────────────────────────────────────
# Stage 2 — PHP on Debian (more reliable library support than Alpine)
# ────────────────────────────────────────────────────────────────────────────
FROM php:8.4-fpm-bookworm

# System packages (Debian bookworm)
RUN apt-get update && apt-get install -y --no-install-recommends \
        nginx \
        supervisor \
        libpq-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libzip-dev \
        libonig-dev \
        libicu-dev \
        zip \
        unzip \
        git \
        curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_pgsql \
        pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install PHP dependencies
COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --optimize-autoloader \
        --no-scripts \
        --no-interaction \
        --prefer-dist

# Copy full application
COPY . .

# Copy compiled frontend assets from node stage
COPY --from=node-builder /app/public/build ./public/build

# Create a minimal .env so artisan doesn't crash during build
RUN cp .env.example .env 2>/dev/null || echo "APP_KEY=" > .env

# Run composer post-install hooks
RUN composer run-script post-autoload-dump --no-interaction 2>/dev/null || true

# Storage & permissions
RUN mkdir -p storage/framework/{sessions,views,cache/data} \
             storage/logs \
             storage/app/public \
             bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 storage bootstrap/cache

# Copy runtime config
COPY docker/nginx.conf       /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini          /usr/local/etc/php/conf.d/custom.ini
COPY docker/start.sh         /start.sh
RUN chmod +x /start.sh

EXPOSE 10000

CMD ["/start.sh"]
