#!/bin/bash
set -e

function cleanup() {
  echo "✅ Bringing application out of maintenance mode..."
  php artisan up
}
trap cleanup EXIT

echo "🚀 Starting VisionLab Deployment..."

# 1. Enter maintenance mode
echo "⚙️ Putting application into maintenance mode..."
php artisan down || true

# 2. Pull latest code from Git
echo "📥 Pulling latest code from GitHub..."
git pull origin main || true

# 3. Install/Update PHP Dependencies
echo "📦 Installing PHP dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader

# 4. Install/Update Node Dependencies & Build Frontend
echo "🎨 Building frontend assets..."
npm install
npm run build

# 5. Clear Caches
echo "🧹 Clearing old caches..."
php artisan optimize:clear
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# 6. Rebuild Caches for Performance
echo "⚡ Rebuilding caches..."
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

# 7. Run Database Migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# 8. Restart Queues
echo "🔄 Restarting queue workers..."
php artisan queue:restart

# 9. Set proper permissions for storage and build (If running as sudo, otherwise skip)
if [ "$(id -u)" -eq 0 ]; then
    echo "🔐 Setting permissions..."
    chown -R www:www public/build storage bootstrap/cache
    chmod -R 775 storage bootstrap/cache
fi

echo "🎉 Deployment successful!"
