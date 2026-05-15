FROM php:8.2-cli

# Install dependency Linux
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Set folder kerja
WORKDIR /var/www

# Copy semua project
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install dependency Laravel
RUN composer install --no-dev --optimize-autoloader

# Install dependency frontend
RUN npm install

# Build Vite
RUN npm run build

# Port Railway
EXPOSE 8080

# Run Laravel
CMD php artisan serve --host=0.0.0.0 --port=8080