#!/bin/bash
set -e

echo "========================================"
echo "  EduTenant ERP — Container Starting"
echo "========================================"

cd /var/www/html

# ── Port setup (Render injects $PORT, default 10000) ────────────────────────
NGINX_PORT="${PORT:-10000}"
echo "[0/6] Nginx will listen on port $NGINX_PORT"
sed -i "s/NGINX_PORT/$NGINX_PORT/g" /etc/nginx/nginx.conf

# ── 1. Generate APP_KEY if not set ───────────────────────────────────────────
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo "[1/6] Generating APP_KEY..."
    php artisan key:generate --force
else
    echo "[1/6] APP_KEY is set. OK."
fi

# ── 2. Wait for PostgreSQL to be ready ───────────────────────────────────────
echo "[2/6] Waiting for database..."
MAX=30
N=0
until php -r "
    \$h  = getenv('DB_HOST');
    \$p  = getenv('DB_PORT') ?: 5432;
    \$u  = getenv('DB_USERNAME');
    \$pw = getenv('DB_PASSWORD');
    \$db = getenv('DB_DATABASE');
    try {
        new PDO(\"pgsql:host=\$h;port=\$p;dbname=\$db\", \$u, \$pw,
            [PDO::ATTR_TIMEOUT => 3]);
        exit(0);
    } catch (Exception \$e) { exit(1); }
" 2>/dev/null; do
    N=$((N+1))
    [ $N -ge $MAX ] && echo "ERROR: DB unreachable after $MAX tries." && exit 1
    echo "  DB not ready (attempt $N/$MAX)..."
    sleep 3
done
echo "[2/6] Database connected. OK."

# ── 3. Migrations ────────────────────────────────────────────────────────────
echo "[3/6] Running migrations..."
php artisan migrate --force --no-interaction
echo "[3/6] Migrations done. OK."

# ── 4. Seed on first deploy ──────────────────────────────────────────────────
echo "[4/6] Checking seed status..."
COUNT=$(php artisan tinker --execute="echo \App\Models\Tenant::count();" 2>/dev/null \
        | grep -E '^[0-9]+$' | tail -1)
COUNT=${COUNT:-0}
if [ "$COUNT" = "0" ]; then
    echo "[4/6] First deploy — seeding demo data..."
    php artisan db:seed --force --no-interaction
    echo "[4/6] Seeded. OK."
else
    echo "[4/6] Already seeded ($COUNT tenants). Skipping."
fi

# ── 5. Storage & permissions ─────────────────────────────────────────────────
echo "[5/6] Preparing storage..."
mkdir -p storage/framework/{sessions,views,cache/data} \
         storage/logs storage/app/public bootstrap/cache
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
php artisan storage:link --force 2>/dev/null || true
echo "[5/6] Storage ready. OK."

# ── 6. Production cache ──────────────────────────────────────────────────────
echo "[6/6] Building caches..."
php artisan config:cache  || true
php artisan route:cache   || true
php artisan view:cache    || true
echo "[6/6] Done. OK."

echo "========================================"
echo "  Starting Nginx + PHP-FPM + Queue Worker"
echo "  Port: $NGINX_PORT"
echo "========================================"
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
