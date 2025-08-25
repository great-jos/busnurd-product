#!/bin/sh

set -e

cd /var/www/html

php artisan migrate --force

export APP_KEY=$(php artisan key:generate --show)

php artisan storage:link || true

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec apache2-foreground