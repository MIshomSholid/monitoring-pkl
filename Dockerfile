FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

WORKDIR /var/www

COPY . .

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader

EXPOSE 8080

CMD php artisan serve --host=0.0.0.0 --port=8080