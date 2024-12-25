FROM php:8.4-cli

# Instalar dependencias esenciales
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git

# Instalar extensiones PHP necesarias
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias
RUN composer install --no-interaction --no-scripts --prefer-dist \
    && COMPOSER_ALLOW_SUPERUSER=1 composer install
RUN php artisan key:generate 

# Exponer puerto 8000
EXPOSE 8000