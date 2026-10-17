FROM php:8.2-fpm-alpine

# Install dependency sistem & driver PostgreSQL
RUN apk add --no-cache \
    postgresql-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl

# Install ekstensi PHP PDO PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql zip bcmath gd

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8000

CMD php artisan config:clear && php artisan cache:clear && php artisan serve --host=0.0.0.0 --port=8000