FROM php:8.3-fpm-alpine

# Instalar dependencias del sistema y Node.js para Vite/Livewire
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    icu-dev \
    oniguruma-dev \
    nodejs \
    npm

# Instalar extensiones de PHP necesarias para Laravel y PostgreSQL
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd intl xml

# Obtener Composer actualizado
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
