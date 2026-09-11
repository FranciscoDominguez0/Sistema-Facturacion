FROM php:8.4-fpm-alpine

# Instalar dependencias de ejecución (Runtime) y compilación (Build)
RUN apk add --no-cache \
    bash git curl zip unzip \
    nodejs npm chromium nss freetype harfbuzz ca-certificates ttf-freefont \
    libpng libjpeg-turbo libpq icu-libs oniguruma libzip \
    && apk add --no-cache --virtual .build-deps \
        libpng-dev libjpeg-turbo-dev freetype-dev libxml2-dev libpq-dev icu-dev oniguruma-dev libzip-dev $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install -j$(nproc) \
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
        opcache \
    && apk del .build-deps \
    && rm -rf /tmp/pear

# Configurar Puppeteer para usar el Chromium del sistema (Alpine)
ENV PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true \
    PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium-browser

# Instalar Puppeteer globalmente para que Browsershot lo tenga siempre disponible
RUN npm install -g puppeteer
ENV NODE_PATH="/usr/lib/node_modules:/usr/local/lib/node_modules"

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
