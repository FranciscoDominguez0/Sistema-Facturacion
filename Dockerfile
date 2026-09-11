FROM php:8.3-fpm-alpine

# Instalar dependencias del sistema requeridas para Laravel, PostgreSQL, Node.js y Puppeteer (Chromium)
RUN apk add --no-cache \
    bash \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libxml2-dev \
    libpq-dev \
    icu-dev \
    oniguruma-dev \
    libzip-dev \
    nodejs \
    npm \
    chromium \
    nss \
    freetype \
    harfbuzz \
    ca-certificates \
    ttf-freefont

# Configurar Puppeteer para usar el Chromium del sistema (Alpine)
ENV PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true \
    PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium-browser

# Instalar Puppeteer globalmente para que Browsershot lo tenga siempre disponible
RUN npm install -g puppeteer
ENV NODE_PATH="/usr/lib/node_modules:/usr/local/lib/node_modules"

# Configurar e instalar extensiones de PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        intl \
        xml \
        zip \
        opcache

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar configuración personalizada de PHP y OPcache
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Copiar script de entrada
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www/html

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
