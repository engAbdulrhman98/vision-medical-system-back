FROM php:8.4-cli-alpine

ENV COMPOSER_ALLOW_SUPERUSER=1

# Install system dependencies & PHP extensions
RUN apk add --no-cache \
    mysql-client \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    libzip-dev \
    git \
    icu-dev \
    oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql gd zip bcmath mbstring intl exif

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Storage permissions
RUN touch database/database.sqlite && chmod -R 777 storage bootstrap/cache database

# Expose Railway PORT
EXPOSE 8080

# Start command
CMD ["sh", "-c", "(php artisan migrate --force; php artisan db:seed --force) & exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]

