# Guía de Despliegue en Servidor de Producción

Esta guía detalla los pasos exactos para subir y poner en marcha el Sistema de Facturación en un servidor real (VPS o dedicado) utilizando la infraestructura de Docker que hemos configurado.

> [!IMPORTANT]
> Asegúrate de tener instalado **Docker** y **Docker Compose** en tu servidor antes de comenzar.

---

## 1. Subir el código al servidor

Tienes dos opciones principales para llevar el código a tu servidor:

**Opción A (Recomendada): Usar Git**
1. Sube tu código a un repositorio privado (GitHub, GitLab, Bitbucket).
2. En tu servidor, clona el repositorio:
   ```bash
   git clone https://github.com/tu-usuario/sistema-facturacion.git /var/www/facturacion
   cd /var/www/facturacion
   ```

**Opción B: Copiar los archivos directamente**
Usa SCP, SFTP o rsync para copiar toda la carpeta del proyecto a tu servidor, omitiendo las carpetas `node_modules` y `vendor`.

---

## 2. Configurar las variables de entorno

El archivo `.env` es el corazón de la configuración y **no debe subirse al repositorio**. Debes crearlo en el servidor:

```bash
cp .env.example .env
```

Abre el archivo `.env` con un editor (como `nano .env`) y ajusta las siguientes variables críticas para producción:

```env
APP_NAME="Vigifact"
APP_ENV=production          # MUY IMPORTANTE: Cambiar de local a production
APP_KEY=                    # Lo generaremos en el paso 4
APP_DEBUG=false             # MUY IMPORTANTE: Apagar para evitar exponer errores
APP_URL=https://tu-dominio.com

# Configuración de Base de Datos (coincidente con compose.yaml)
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=Sistema-Facturacion
DB_USERNAME=postgres
DB_PASSWORD=tu_contraseña_super_segura
```

---

## 3. Levantar los contenedores

Una vez configurado el `.env`, usaremos Docker Compose para construir la imagen (la cual instalará PHP, Nginx, Node.js y Puppeteer de forma automática) y levantar los servicios.

Ejecuta el siguiente comando en la raíz del proyecto:

```bash
docker compose up -d --build
```
*Nota: La primera vez tardará un par de minutos mientras descarga las imágenes de Linux y compila las dependencias.*

---

## 4. Instalar Dependencias y Compilar (Primer Uso)

Como acabas de clonar el código desde Git, necesitas descargar las librerías de PHP (Composer) y compilar tus estilos y Javascript (Vite/Tailwind). Ejecuta estos dos comandos:

```bash
docker compose exec app composer install --no-dev --optimize-autoloader
docker compose exec app npm install
docker compose exec app npm run build
```

---

## 5. Inicializar Laravel

Con las librerías instaladas, procedemos a preparar la base de datos y la seguridad:

**Generar la clave de encriptación:**
```bash
docker compose exec app php artisan key:generate
```

**Crear las tablas en la base de datos:**
```bash
docker compose exec app php artisan migrate --force
```

*(Opcional) Para crear únicamente el usuario Administrador y la configuración inicial de la empresa (sin datos basura ni facturas demo):*
```bash
docker compose exec app php artisan db:seed --class=AdminUserSeeder
docker compose exec app php artisan db:seed --class=EmpresaSeeder
```

**Crear el enlace simbólico para las imágenes (Logos y Avatares):**
```bash
docker compose exec app php artisan storage:link
```

---

## 5. Optimización para Producción (Caché)

Para que el sistema vuele 🚀, debes cachear las rutas, vistas y configuraciones. Ejecuta este comando maestro:

```bash
docker compose exec app php artisan optimize
```

> [!TIP]
> Si en el futuro modificas tu archivo `.env`, deberás correr `docker compose exec app php artisan optimize:clear` para que tome los cambios.

---

## 6. Configurar Nginx Proxy Manager / Dominio

Como usas **Nginx Proxy Manager (NPM)** con interfaz gráfica, la configuración es muy sencilla porque NPM actúa como intermediario HTTP, no como FastCGI. Por esta razón, hemos restaurado el contenedor Nginx interno en el puerto **8088**.

En tu Nginx Proxy Manager, configura tu dominio así:

- **Scheme:** `http`
- **Forward Hostname / IP:** La IP de tu servidor (ej: `192.168.110.109`)
- **Forward Port:** `8088`

> [!IMPORTANT]
> Recuerda ir a la pestaña **SSL** en tu Nginx Proxy Manager, seleccionar "Request a new SSL Certificate" y activar "Force SSL". Puppeteer y muchas funciones web modernas requieren HTTPS para funcionar correctamente.

---

## 📝 Resumen de Mantenimiento

- **Ver logs del sistema:** `docker compose logs -f`
- **Apagar el sistema:** `docker compose down`
- **Reiniciar el sistema:** `docker compose restart`
- **Actualizar el sistema (después de un git pull):**
  ```bash
  docker compose up -d --build
  docker compose exec app php artisan migrate --force
  docker compose exec app php artisan optimize
  ```
