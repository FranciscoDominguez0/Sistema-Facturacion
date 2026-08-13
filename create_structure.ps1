php artisan make:model Empresa
php artisan make:model Cliente
php artisan make:model Vendedor
php artisan make:model Producto
php artisan make:model Factura
php artisan make:model FacturaItem
php artisan make:model Gasto

php artisan make:policy FacturaPolicy --model=Factura
php artisan make:policy ClientePolicy --model=Cliente
php artisan make:policy ProductoPolicy --model=Producto

php artisan make:controller PdfFacturaController

php artisan make:livewire Dashboard
php artisan make:livewire Auth/Login
php artisan make:livewire Auth/ForgotPassword
php artisan make:livewire Auth/ResetPassword
php artisan make:livewire Configuracion/EmpresaForm
php artisan make:livewire Clientes/ClienteIndex
php artisan make:livewire Clientes/ClienteForm
php artisan make:livewire Clientes/ClienteShow
php artisan make:livewire Productos/ProductoIndex
php artisan make:livewire Productos/ProductoForm
php artisan make:livewire Vendedores/VendedorIndex
php artisan make:livewire Vendedores/VendedorForm
php artisan make:livewire Facturas/FacturaIndex
php artisan make:livewire Facturas/FacturaForm
php artisan make:livewire Facturas/FacturaShow
php artisan make:livewire Gastos/GastoIndex
php artisan make:livewire Gastos/GastoForm
php artisan make:livewire Roles/RolIndex
php artisan make:livewire Roles/RolForm
php artisan make:livewire Roles/UsuarioRolAssign

mkdir -Force app/Enums
Set-Content -Path app/Enums/EstadoFactura.php -Value "<?php\
\
namespace App\Enums;\
\
enum EstadoFactura: string\
{\
    case PENDIENTE = 'Pendiente';\
    case PAGADA = 'Pagada';\
    case ANULADA = 'Anulada';\
}\"
Set-Content -Path app/Enums/TipoProducto.php -Value "<?php\
\
namespace App\Enums;\
\
enum TipoProducto: string\
{\
    case PRODUCTO = 'Producto';\
    case SERVICIO = 'Servicio';\
}\"

mkdir -Force app/Services
Set-Content -Path app/Services/FacturaService.php -Value "<?php\
\
namespace App\Services;\
\
class FacturaService\
{\
}\"
Set-Content -Path app/Services/PdfFacturaService.php -Value "<?php\
\
namespace App\Services;\
\
class PdfFacturaService\
{\
}\"
Set-Content -Path app/Services/ProductoImagenService.php -Value "<?php\
\
namespace App\Services;\
\
class ProductoImagenService\
{\
}\"
Set-Content -Path app/Services/ReporteService.php -Value "<?php\
\
namespace App\Services;\
\
class ReporteService\
{\
}\"
