#!/bin/bash

echo "📥 Pulling latest code from Git..."
git pull origin main

echo "🔄 Restarting PHP-FPM..."
sudo systemctl restart php8.3-fpm

echo "🧹 Clearing Laravel caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "✅ Deployment script completed."