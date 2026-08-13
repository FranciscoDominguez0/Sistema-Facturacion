# PLAN DE DESARROLLO --- SISTEMA DE FACTURACION Y VENTAS

**Stack:** Laravel + Blade + Livewire + Tailwind CSS + PostgreSQL\
**Referencia UX:** Invoice Ninja --- simple, rapido, pocos clics y minimos campos obligatorios.\
**Version:** 7.1 --- Plan de trabajo completo (base escalable, control de descuentos por rol/usuario, imagen en productos y detalle de endpoints por modulo)\
**Alcance actual:** factura normal interna en PDF. **Sin facturacion electronica ni integracion con DGI. Sin registro publico de usuarios.**\
**Nota de escalabilidad:** este documento describe la v1 (MVP). Cada fase nueva se agrega siguiendo el mismo patron (ver Seccion 3.1) sin romper lo ya construido.

## Novedades de la version 7.1

1. Base de datos actualizada de MySQL/MariaDB a **PostgreSQL** en todo el plan (stack, pasos, `.env`, verificaciones y checklist).
2. Se eliminan del alcance actual los modulos de **Envios**, **Devoluciones** e **Inventario**.
3. Se mantiene la estructura completa por fases, endpoints, modelo de datos y criterios de calidad.

------------------------------------------------------------------------

# 0. REGLAS DEL PROYECTO

Antes de programar cualquier modulo, estas reglas deben mantenerse en todo el sistema:

1. No existe registro publico.
2. El login solamente permite iniciar sesion y recuperar contraseña.
3. Las cuentas nuevas las crea un Administrador desde el sistema.
4. La factura actual es una factura normal/interna en PDF.
5. No implementar por ahora DGI, CUFE, XML firmado, autorizacion fiscal ni facturacion electronica.
6. Crear una venta debe ser rapido:
   - Buscar cliente existente.
   - O crear cliente desde la misma venta.
   - **Solo el nombre del cliente es obligatorio.**
7. Los datos opcionales se pueden completar posteriormente.
8. El catalogo de productos/servicios es reutilizable, pero una linea de venta tambien puede escribirse manualmente.
9. No borrar productos, clientes o facturas historicas fisicamente si eso rompe la trazabilidad; preferir estados activo/inactivo o anulado.
10. La logica importante debe vivir en Services, no dentro de las vistas.
11. Las operaciones criticas deben utilizar transacciones.
12. Los permisos se validan por capacidad (`can`) y no por nombres de roles escritos directamente en la logica.
13. El diseño debe ser responsive y consistente.
14. Cada fase debe quedar funcional y probada antes de comenzar la siguiente.
15. **Los descuentos nunca son de libre acceso.** Todo descuento pasa por validacion de permiso y de limite maximo antes de guardarse.
16. El sistema se construye pensando en que crecera. Cada modulo nuevo debe poder agregarse siguiendo el mismo patron (Modelo + Migracion + Policy + Service + Livewire) sin modificar los modulos existentes.
17. Toda ruta interna sigue una convencion de nombres fija (`modulo.accion`) para futuras integraciones.

------------------------------------------------------------------------

# 1. OBJETIVO DEL SISTEMA

Construir un sistema de ventas y facturacion para una PYME que permita:

- Iniciar sesion de forma segura.
- Configurar la empresa.
- Crear clientes rapidamente.
- Crear productos y servicios.
- Crear vendedores/usuarios internos.
- Crear ventas/facturas rapidamente.
- Crear un cliente sin abandonar el formulario de venta.
- Calcular automaticamente subtotal, impuesto y total.
- Aplicar descuentos de forma controlada.
- Controlar estado de facturas.
- Generar factura PDF profesional.
- Registrar gastos.
- Gestionar roles y permisos.
- Consultar dashboard.

------------------------------------------------------------------------

# 2. EXPERIENCIA DE USUARIO OBJETIVO

El sistema debe sentirse como una aplicacion administrativa sencilla, no como un formulario burocratico.

## Flujo principal

```text
Login
  ↓
Dashboard
  ↓
Nueva venta
  ↓
Buscar cliente
  ├── Cliente existe → seleccionar
  └── Cliente no existe → "Crear cliente" → solo nombre
  ↓
Agregar productos/servicios
  ├── Buscar producto existente
  └── Agregar linea manual
  ↓
Recalcular subtotal/impuesto/total
  ↓
Guardar venta
  ↓
Factura creada
  ↓
Ver / imprimir / descargar PDF
  ↓
Registrar pago o cambiar estado cuando corresponda
```

## Regla de oro

**Nunca obligar al usuario a completar informacion que no necesita para terminar una venta.**

------------------------------------------------------------------------

# 3. STACK TECNOLOGICO

| Area | Tecnologia |
|---|---|
| Backend | Laravel |
| Vistas | Blade |
| Interactividad | Livewire |
| CSS | Tailwind CSS |
| Base de datos | PostgreSQL |
| ORM | Eloquent |
| Autenticacion | Laravel Breeze |
| Roles/permisos | Spatie Laravel Permission |
| PDF | barryvdh/laravel-dompdf |
| Archivos | Laravel Storage |
| Correo | Laravel Notifications + SMTP |
| Pruebas | PHPUnit / Laravel Feature Tests |
| Control de versiones | Git |

------------------------------------------------------------------------

# 3.1 PRINCIPIOS DE ESCALABILIDAD

## 3.1.1 Todo modulo nuevo sigue el mismo molde

```text
1. Migracion        → database/migrations/xxxx_create_xxx_table.php
2. Modelo           → app/Models/Xxx.php
3. Enum (si aplica) → app/Enums/EstadoXxx.php
4. Policy           → app/Policies/XxxPolicy.php
5. Service          → app/Services/XxxService.php
6. Livewire         → app/Livewire/Xxx/XxxIndex.php, XxxForm.php, XxxShow.php
7. Rutas            → routes/web.php (xxx.index, xxx.crear, xxx.show)
8. Permisos         → database/seeders/PermissionSeeder.php
```

## 3.1.2 Convencion de nombres

| Elemento | Convencion | Ejemplo |
|---|---|---|
| Rutas web | `modulo.accion` | `facturas.crear` |
| Permisos | `modulo.accion` | `facturas.descuento.aplicar` |
| Services | `XxxService` | `FacturaService` |
| Enums | `EstadoXxx` / `TipoXxx` | `EstadoFactura` |

------------------------------------------------------------------------

# 4. COMO EMPEZAR --- PASO A PASO

No empezar creando modulos manualmente. Primero dejar el proyecto base funcionando.

## Paso 1 --- Login primero

Entrar al proyecto existente y validar autenticacion:

```bash
cd ruta/de/tu/sistema-facturacion
php artisan route:list
```

Si no existe login/auth, instalar:

```bash
composer require laravel/breeze --dev
php artisan breeze:install livewire
npm install
npm run build
```

Verificar en navegador:

```text
http://localhost:8000/login
```

No avanzar hasta confirmar login/logout y sin registro publico.

## Paso 2 --- Requisitos

Instalar/verificar:

- PHP 8.3+
- Composer
- Node.js + npm
- PostgreSQL
- Git

Comprobar:

```bash
php -v
composer -V
node -v
npm -v
psql --version
```

## Paso 3 --- Configurar `.env` para PostgreSQL

```env
APP_NAME="Sistema de Facturacion"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sistema_facturacion
DB_USERNAME=tu_usuario_pg
DB_PASSWORD=tu_password_pg
```

Aplicar:

```bash
php artisan optimize:clear
```

## Paso 4 --- Preparar base de datos PostgreSQL

```sql
CREATE ROLE facturacion_user WITH LOGIN PASSWORD 'cambia_esta_clave';
CREATE DATABASE sistema_facturacion OWNER facturacion_user;
GRANT ALL PRIVILEGES ON DATABASE sistema_facturacion TO facturacion_user;
```

Validar conexion:

```bash
psql -h 127.0.0.1 -p 5432 -U facturacion_user -d sistema_facturacion
```

## Paso 5 --- Instalar permisos y PDF

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

composer require barryvdh/laravel-dompdf
```

## Paso 6 --- Migrar

```bash
php artisan migrate
```

## Paso 7 --- Compilar frontend

```bash
npm run build
```

Durante desarrollo:

```bash
npm run dev
```

## Paso 8 --- Crear primer administrador

Crear `database/seeders/AdminUserSeeder.php`.

Debe crear:

- Nombre.
- Correo.
- Contraseña hasheada.
- Rol Administrador.

Ejecutar:

```bash
php artisan db:seed --class=AdminUserSeeder
```

## Paso 9 --- Storage

```bash
php artisan storage:link
```

## Paso 10 --- Ejecutar

```bash
php artisan serve
```

Abrir:

```text
http://localhost:8000/login
```

Verificar inmediatamente:

- El login funciona.
- No existe enlace de registro.
- `/register` no esta disponible.
- Se puede cerrar sesion.
- La recuperacion de contraseña esta preparada.

**No continuar a la siguiente fase hasta que esto funcione.**

------------------------------------------------------------------------

# 5. ESTRUCTURA PROFESIONAL DEL PROYECTO

```text
sistema-facturacion/
├── app/
│   ├── Enums/
│   │   ├── EstadoFactura.php
│   │   └── TipoProducto.php
│   ├── Http/
│   │   └── Controllers/
│   │       └── PdfFacturaController.php
│   ├── Livewire/
│   │   ├── Dashboard.php
│   │   ├── Auth/
│   │   │   ├── Login.php
│   │   │   ├── ForgotPassword.php
│   │   │   └── ResetPassword.php
│   │   ├── Configuracion/
│   │   │   └── EmpresaForm.php
│   │   ├── Clientes/
│   │   │   ├── ClienteIndex.php
│   │   │   ├── ClienteForm.php
│   │   │   └── ClienteShow.php
│   │   ├── Productos/
│   │   │   ├── ProductoIndex.php
│   │   │   └── ProductoForm.php
│   │   ├── Vendedores/
│   │   │   ├── VendedorIndex.php
│   │   │   └── VendedorForm.php
│   │   ├── Facturas/
│   │   │   ├── FacturaIndex.php
│   │   │   ├── FacturaForm.php
│   │   │   └── FacturaShow.php
│   │   ├── Gastos/
│   │   │   ├── GastoIndex.php
│   │   │   └── GastoForm.php
│   │   └── Roles/
│   │       ├── RolIndex.php
│   │       ├── RolForm.php
│   │       └── UsuarioRolAssign.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Empresa.php
│   │   ├── Cliente.php
│   │   ├── Vendedor.php
│   │   ├── Producto.php
│   │   ├── Factura.php
│   │   ├── FacturaItem.php
│   │   └── Gasto.php
│   ├── Policies/
│   │   ├── FacturaPolicy.php
│   │   ├── ClientePolicy.php
│   │   └── ProductoPolicy.php
│   └── Services/
│       ├── FacturaService.php
│       ├── PdfFacturaService.php
│       ├── ProductoImagenService.php
│       └── ReporteService.php
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── resources/views/
│   ├── layouts/
│   ├── components/
│   ├── livewire/
│   └── pdf/
├── routes/
│   ├── web.php
│   └── auth.php
└── tests/
    ├── Feature/
    └── Unit/
```

------------------------------------------------------------------------

# FASE 1 --- Base del Proyecto

## Objetivo

Dejar Laravel instalado, conectado a PostgreSQL y con dependencias principales funcionando.

## Comandos utiles

```bash
composer create-project laravel/laravel sistema-facturacion
cd sistema-facturacion

composer require laravel/breeze --dev
php artisan breeze:install livewire

composer require spatie/laravel-permission
composer require barryvdh/laravel-dompdf

npm install
npm run build

php artisan migrate
php artisan storage:link
```

## Verificacion

```bash
php artisan about
php artisan migrate:status
php artisan route:list
```

------------------------------------------------------------------------

# FASE 2 --- Interfaz de Login

## Objetivo

Tener login profesional, sencillo y seguro, **sin registro publico**.

## Lógica --- que debe hacer

- Mantener login, logout y recuperacion de contraseña.
- Eliminar/desactivar rutas `/register`.
- Autenticar con Breeze + Livewire.

------------------------------------------------------------------------

# FASE 3 --- Layout General y Navegacion

## Objetivo

Crear la estructura visual que usaran todos los modulos.

## Menu inicial

```text
Dashboard

Ventas
  ├── Facturas
  └── Nueva venta

Clientes
Productos y Servicios
Vendedores
Gastos
Reportes

Configuracion
  ├── Empresa
  └── Roles y permisos
```

------------------------------------------------------------------------

# FASE 4 --- Empresa y Configuracion

## Objetivo

Configurar una unica empresa que alimentara facturas y PDF.

## Comandos utiles

```bash
php artisan make:model Empresa -m
php artisan make:livewire Configuracion/EmpresaForm
php artisan make:seeder EmpresaSeeder
```

**No agregar campos DGI, CUFE, XML ni autorizacion fiscal.**

------------------------------------------------------------------------

# FASE 5 --- Clientes

## Objetivo

Crear modulo de clientes extremadamente sencillo.

## Regla principal

```text
nombre *
```

Todo lo demas puede completarse despues.

------------------------------------------------------------------------

# FASE 6 --- Productos y Servicios

## Objetivo

Crear catalogo opcional y reutilizable.

## Incluye

- Imagen de producto opcional.
- Miniatura en listado.
- Eliminacion de imagen anterior al reemplazar.
- Servicio `ProductoImagenService` para guardar/eliminar archivos.

------------------------------------------------------------------------

# FASE 7 --- Vendedores y Usuarios Internos

## Objetivo

Eliminar registro publico y centralizar creacion de cuentas desde administracion.

## Flujo

```text
User
  ↓
Asignar rol
  ↓
Crear Vendedor
  ↓
Relacionar user_id
```

------------------------------------------------------------------------

# FASE 8 --- Roles y Permisos

## Objetivo

Controlar que puede hacer cada usuario.

## Permisos minimos

```text
empresa.configurar

clientes.ver
clientes.gestionar

productos.ver
productos.gestionar

vendedores.gestionar

facturas.ver
facturas.crear
facturas.editar
facturas.anular
facturas.pdf

facturas.descuento.aplicar
facturas.descuento.ilimitado
descuentos.configurar

gastos.ver
gastos.ver_todos
gastos.crear
gastos.editar

reportes.ver

roles.gestionar
admin.acceso
```

------------------------------------------------------------------------

# FASE 9 --- Ventas y Facturas

## Objetivo

Nucleo del sistema: creacion de venta rapida con pocos campos.

## Service obligatorio

`app/Services/FacturaService.php`

Debe encargarse de:

```text
crear()
calcularTotales()
validarDescuento()
generarNumero()
actualizar()
anular()
cambiarEstado()
```

------------------------------------------------------------------------

# FASE 9.1 --- Descuentos (control por rol/usuario)

## Objetivo

Permitir descuentos controlados por permiso y tope por vendedor.

## Regla clave

Si no tiene `facturas.descuento.aplicar`, el descuento se fuerza a `0` en backend.

Si tiene permiso limitado, nunca puede superar `vendedores.descuento_maximo_porcentaje`.

------------------------------------------------------------------------

# FASE 10 --- Detalle, Estados y PDF de Factura

## Estados

```text
Pendiente
Pagada
Anulada
```

## PDF

Archivo:

```text
resources/views/pdf/factura.blade.php
```

**No incluir informacion de facturacion electronica.**

------------------------------------------------------------------------

# FASE 11 --- Gastos

## Objetivo

Registrar egresos basicos para ver ingresos frente a gastos.

------------------------------------------------------------------------

# FASE 12 --- Dashboard y Reportes

## Objetivo

Mostrar informacion util sin saturar pantalla.

## Service

```text
app/Services/ReporteService.php
```

------------------------------------------------------------------------

# FASE 13 --- Pruebas

## Estructura

```text
tests/
├── Feature/
│   ├── Auth/
│   ├── Clientes/
│   ├── Productos/
│   ├── Facturas/
│   └── Gastos/
└── Unit/
    └── FacturaServiceTest.php
```

------------------------------------------------------------------------

# FASE 14 --- Seguridad y Pulido

## Checklist

- [ ] `.env` nunca se sube a Git.
- [ ] Contraseñas hasheadas.
- [ ] Validacion en Livewire y Services.
- [ ] Autorizacion por permisos.
- [ ] CSRF.
- [ ] Transacciones en operaciones criticas.
- [ ] Indices y foreign keys.
- [ ] Paginacion en listados.

------------------------------------------------------------------------

# FASE 15 --- Produccion y Despliegue

## Antes del despliegue

```bash
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

## Variables importantes

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com
```

------------------------------------------------------------------------

# 18. MODELO DE DATOS FINAL

## `users`

```text
id
name
email
password
remember_token
timestamps
```

## `empresas`

```text
id
nombre
identificacion_fiscal nullable
logo_path nullable
moneda
simbolo_moneda
impuesto_nombre nullable
impuesto_porcentaje
prefijo_factura
siguiente_numero_factura
color_primario
pie_pagina_pdf nullable
timestamps
```

## `clientes`

```text
id
nombre
identificacion nullable
email nullable
telefono nullable
direccion nullable
activo
timestamps
```

## `vendedores`

```text
id
user_id
codigo nullable
comision_porcentaje nullable
descuento_maximo_porcentaje default 0
activo
timestamps
```

## `productos`

```text
id
nombre
descripcion nullable
codigo nullable
precio
tipo
aplica_impuesto
imagen_path nullable
activo
timestamps
```

## `facturas`

```text
id
numero_factura unique
cliente_id
vendedor_id
fecha_emision
fecha_vencimiento nullable
subtotal
descuento_porcentaje default 0
descuento_total default 0
impuesto
total
estado
notas nullable
timestamps
```

## `factura_items`

```text
id
factura_id
producto_id nullable
descripcion
cantidad
precio_unitario
descuento_porcentaje default 0
descuento_monto default 0
subtotal_linea
timestamps
```

## `gastos`

```text
id
concepto
categoria
monto
fecha
registrado_por
comprobante nullable
timestamps
```

------------------------------------------------------------------------

# 19. RELACIONES PRINCIPALES

```text
Empresa
  └── configuracion singleton

User
  ├── Vendedor
  ├── Gastos
  └── Roles

Cliente
  └── Facturas

Vendedor
  └── Facturas

Producto
  └── FacturaItems

Factura
  ├── Cliente
  ├── Vendedor
  └── FacturaItems

FacturaItem
  ├── Factura
  └── Producto
```

------------------------------------------------------------------------

# 20. RUTAS Y ENDPOINTS DEL SISTEMA

## 20.1 Convencion

Rutas nombradas por patron `modulo.accion`.

## 20.2 Listado completo (v1)

```text
GET  /login                              login
GET  /logout                             logout
GET  /forgot-password                    password.request
GET  /reset-password/{token}             password.reset

GET  /dashboard                          dashboard

GET  /configuracion/empresa              empresa.editar

GET  /clientes                           clientes.index
GET  /clientes/crear                     clientes.crear
GET  /clientes/{cliente}                 clientes.show

GET  /productos                          productos.index
GET  /productos/crear                    productos.crear
GET  /productos/{producto}               productos.show

GET  /vendedores                         vendedores.index
GET  /vendedores/crear                   vendedores.crear
GET  /vendedores/{vendedor}/editar       vendedores.editar

GET  /facturas                           facturas.index
GET  /facturas/crear                     facturas.crear
GET  /facturas/{factura}                 facturas.show
GET  /facturas/{factura}/pdf             facturas.pdf

GET  /gastos                             gastos.index
GET  /gastos/crear                       gastos.crear

GET  /roles                              roles.index
GET  /roles/crear                        roles.crear
```

------------------------------------------------------------------------

# 21. ORDEN EXACTO DE DESARROLLO

```text
FASE 1  → Base Laravel + PostgreSQL
FASE 2  → Login sin registro
FASE 3  → Layout y navegacion
FASE 4  → Empresa
FASE 5  → Clientes
FASE 6  → Productos y servicios
FASE 7  → Vendedores
FASE 8  → Roles y permisos
FASE 9  → Ventas y facturas
FASE 9.1 → Descuentos (permiso + tope por usuario)
FASE 10 → Detalle y PDF
FASE 11 → Gastos
FASE 12 → Dashboard
FASE 13 → Pruebas
FASE 14 → Seguridad y pulido
FASE 15 → Produccion
```

------------------------------------------------------------------------

# 22. CRITERIO PARA PASAR DE UNA FASE A OTRA

- [ ] Archivos indicados existen.
- [ ] Migraciones funcionan.
- [ ] Relaciones funcionan.
- [ ] Pantalla funciona.
- [ ] Validacion funciona.
- [ ] Permisos funcionan.
- [ ] No hay errores en consola ni logs.
- [ ] Se probo flujo principal y un caso incorrecto.
- [ ] No rompe fases anteriores.

------------------------------------------------------------------------

# 23. BUENAS PRACTICAS DE ARQUITECTURA

## Livewire

Usar para busqueda en vivo, modales, alta rapida de clientes, lineas dinamicas y filtros.

## Services

Usar para crear facturas, numerar, validar descuentos, generar PDF y reportes.

## Models

Relaciones, casts, scopes.

## Blade

Presentacion y componentes visuales.

------------------------------------------------------------------------

# 24. REGLA ESPECIAL PARA FACTURAS

Creacion atomica mediante transaccion:

```php
DB::transaction(function () {
    // generar numero
    // crear factura
    // crear items
});
```

------------------------------------------------------------------------

# 25. REGLA ESPECIAL PARA CLIENTES

Permitir alta rapida desde venta con solo nombre y seleccion automatica.

------------------------------------------------------------------------

# 26. REGLA ESPECIAL PARA PRODUCTOS

Producto es ayuda, no obligacion. Debe permitir linea manual en factura.

------------------------------------------------------------------------

# 27. REGLA ESPECIAL PARA ESTADOS

## Factura

```text
Pendiente
Pagada
Anulada
```

------------------------------------------------------------------------

# 28. FUNCIONES QUE SE DEJAN PARA EL FUTURO

No implementar en esta version:

- Facturacion electronica.
- Integracion DGI.
- CUFE.
- XML firmado.
- Autorizacion fiscal.
- Multiempresa/multi-tenant.
- Integraciones fiscales externas.
- Envios, devoluciones e inventario (fuera de alcance actual).

------------------------------------------------------------------------

# 29. CHECKLIST FINAL DEL SISTEMA

## Base

- [ ] Laravel funcionando.
- [ ] PostgreSQL funcionando.
- [ ] Livewire funcionando.
- [ ] Tailwind funcionando.
- [ ] Git configurado.

## Autenticacion

- [ ] Login.
- [ ] Logout.
- [ ] Recordarme.
- [ ] Recuperacion de contraseña.
- [ ] Sin registro publico.
- [ ] Admin inicial por Seeder.

## Administracion

- [ ] Empresa.
- [ ] Usuarios.
- [ ] Vendedores.
- [ ] Roles.
- [ ] Permisos.

## Ventas

- [ ] Clientes.
- [ ] Productos.
- [ ] Servicios.
- [ ] Nueva venta.
- [ ] Cliente rapido.
- [ ] Lineas dinamicas.
- [ ] Calculo automatico.
- [ ] Numero correlativo.
- [ ] Estados.
- [ ] Descuento respeta permiso y tope.

## Documentos

- [ ] PDF.
- [ ] Impresion.

## Gestion

- [ ] Dashboard.
- [ ] Reportes basicos.
- [ ] Filtros.
- [ ] Busqueda.
- [ ] Paginacion.

## Calidad

- [ ] Tests.
- [ ] Seguridad.
- [ ] Backups.
- [ ] HTTPS.
- [ ] SMTP.
- [ ] Produccion.

------------------------------------------------------------------------

# 30. DEFINICION DE LISTO PARA USAR

Un Administrador puede:

```text
1. Iniciar sesion.
2. Configurar empresa.
3. Crear vendedor.
4. Crear o buscar cliente.
5. Crear venta.
6. Crear cliente desde la venta.
7. Agregar productos o lineas manuales.
8. Guardar factura.
9. Ver factura.
10. Descargar/imprimir PDF.
11. Marcarla como pagada.
12. Registrar gastos.
13. Consultar dashboard.
```

Y un Vendedor puede:

```text
1. Iniciar sesion.
2. Crear venta.
3. Crear cliente rapido.
4. Seleccionar productos.
5. Aplicar descuento segun su tope autorizado.
6. Emitir factura.
7. Descargar/imprimir PDF.
8. Consultar operaciones permitidas.
```

------------------------------------------------------------------------

# 31. PRINCIPIO FINAL DEL PROYECTO

La prioridad no es tener cientos de funciones.

La prioridad es que el usuario pueda hacer esto con el menor numero de clics posible:

```text
NUEVA VENTA
    ↓
CLIENTE
    ↓
PRODUCTOS
    ↓
TOTAL
    ↓
GUARDAR
    ↓
FACTURA PDF
```

Todo lo demas debe complementar ese flujo.

**El sistema debe ser sencillo para el vendedor y completo para el administrador.**
