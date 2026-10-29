# ────────────────────────────────────────────────────────────────────────────
# Stage 1 — Node: compile frontend assets (Vite build)
# ────────────────────────────────────────────────────────────────────────────
FROM node:20-alpine AS node-builder

WORKDIR /app
COPY package*.json ./
RUN npm ci --ignore-scripts
COPY . .
RUN npm run build

# ────────────────────────────────────────────────────────────────────────────
# Stage 2 — PHP: production image
# ────────────────────────────────────────────────────────────────────────────
FROM php:8.4-fpm-alpine

# System packages
RUN apk add --no-cache \
        nginx \
        supervisor \
        mysql-client \
        postgresql-client \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libzip-dev \
        zip \
        unzip \
        git \
        curl \
        oniguruma-dev \
        icu-dev \
        shadow \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        pdo_pgsql \
        pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install PHP dependencies (no dev, optimized)
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

# Create a .env so artisan commands don't fail during build
# (real values come from Render environment variables at runtime)
RUN cp .env.example .env 2>/dev/null || echo "APP_KEY=" > .env

# Run composer post-install hooks
RUN composer run-script post-autoload-dump --no-interaction 2>/dev/null || true

# Ensure storage and cache dirs exist with correct permissions
RUN mkdir -p storage/framework/{sessions,views,cache/data} \
             storage/logs \
             storage/app/public \
             bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 storage bootstrap/cache

# Copy runtime config files
COPY docker/nginx.conf      /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini         /usr/local/etc/php/conf.d/custom.ini
COPY docker/start.sh        /start.sh
RUN chmod +x /start.sh

# Render free tier uses port 10000 by default
# (nginx.conf listens on PORT env var; defaults to 10000)
EXPOSE 10000

CMD ["/start.sh"]
