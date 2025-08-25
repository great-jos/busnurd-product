# Build frontend assets
FROM node:20 AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm install
COPY . .
RUN npm run build

# Use official PHP 8.4 with Apache
FROM php:8.4-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache rewrite
RUN a2enmod rewrite

# Set working dir
WORKDIR /var/www/html

# Environment (SQLite DB in storage)
ENV APP_ENV=production \
    APP_DEBUG=false \
    DB_CONNECTION=sqlite \
    CACHE_DRIVER=file \
    SESSION_DRIVER=cookie \
    QUEUE_CONNECTION=sync \
    LOG_CHANNEL=stderr \
    DB_DATABASE=/var/www/html/database/database.sqlite

# Copy composer first for caching
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer 

# Copy app files
COPY . .

# Copy frontend build output
COPY --from=frontend /app/public/build ./public/build

# Create sqlite database file
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist \
    && mkdir -p database \
    && touch database/database.sqlite \
    && chown -R www-data:www-data database \
    && chown -R www-data:www-data storage \
    && chmod -R 775 database storage

# Enable Apache Rewrite
COPY ./laravel.conf /etc/apache2/sites-available/laravel.conf
RUN a2ensite laravel && a2dissite 000-default

COPY ./entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Start Apache (default CMD in php:apache is already apache2-foreground)
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]