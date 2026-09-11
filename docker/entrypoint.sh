#!/bin/sh
set -e

# Crear enlace simbólico de storage si no existe
if [ ! -L /var/www/html/public/storage ]; then
    php artisan storage:link --force || true
fi

# Asegurar que existan los directorios críticos de Laravel (especialmente en el primer clonado)
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

# Ajustar permisos de directorios de escritura
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Ejecutar el comando principal (por defecto php-fpm)
exec "$@"
