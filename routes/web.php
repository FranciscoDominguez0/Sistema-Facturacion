<?php

use App\Http\Controllers\PdfFacturaController;
use App\Http\Middleware\PreventBackHistory;
use App\Livewire\Clientes\ClienteForm;
use App\Livewire\Clientes\ClienteIndex;
use App\Livewire\Clientes\ClienteShow;
use App\Livewire\Configuracion\EmpresaForm;
use App\Livewire\Configuracion\FacturacionIndex;
use App\Livewire\Configuracion\ImpuestoIndex;
use App\Livewire\Dashboard;
use App\Livewire\Facturas\FacturaForm;
use App\Livewire\Facturas\FacturaIndex;
use App\Livewire\Facturas\FacturaPdf;
use App\Livewire\Gastos\GastoForm;
use App\Livewire\Gastos\GastoIndex;
use App\Livewire\Productos\ProductoForm;
use App\Livewire\Productos\ProductoIndex;
use App\Livewire\Profile;
use App\Livewire\Roles\RolIndex;
use App\Livewire\Roles\UsuarioIndex;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware(['auth', PreventBackHistory::class])->group(function () {
    Route::get('dashboard', Dashboard::class)
        ->middleware(['verified'])
        ->name('dashboard');

    Route::get('profile', Profile::class)
        ->name('profile');

    Route::get('clientes', ClienteIndex::class)
        ->middleware('can:clientes.ver')
        ->name('clientes');

    Route::get('clientes/create', ClienteForm::class)
        ->middleware('can:clientes.crear')
        ->name('clientes.create');

    Route::get('clientes/{cliente}/edit', ClienteForm::class)
        ->middleware('can:clientes.editar')
        ->name('clientes.edit');

    Route::get('clientes/{cliente}', ClienteShow::class)
        ->middleware('can:clientes.ver')
        ->name('clientes.show');

    Route::get('productos', ProductoIndex::class)
        ->middleware('can:productos.ver')
        ->name('productos.index');

    Route::get('productos/create', ProductoForm::class)
        ->middleware('can:productos.crear')
        ->name('productos.create');

    Route::get('productos/{producto}/edit', ProductoForm::class)
        ->middleware('can:productos.editar')
        ->name('productos.edit');

    Route::get('facturas', FacturaIndex::class)
        ->name('facturas');

    Route::get('facturas/crear', FacturaForm::class)
        ->middleware('can:facturas.crear')
        ->name('facturas.crear');

    Route::get('facturas/{factura}/edit', FacturaForm::class)
        ->middleware('can:facturas.editar')
        ->name('facturas.edit');

    Route::get('facturas/{factura}/pdf-vista', FacturaPdf::class)
        ->middleware('can:facturas.ver')
        ->name('facturas.pdf.vista');

    Route::get('facturas/{factura}/pdf', [PdfFacturaController::class, 'mostrar'])
        ->middleware('can:facturas.ver')
        ->name('facturas.pdf');

    Route::get('facturas/{factura}/preview', [PdfFacturaController::class, 'preview'])
        ->middleware('can:facturas.ver')
        ->name('facturas.preview');

    Route::get('gastos', GastoIndex::class)
        ->name('gastos')
        ->middleware('can:gastos.ver');

    Route::get('gastos/crear', GastoForm::class)
        ->name('gastos.crear')
        ->middleware('can:gastos.crear');

    Route::get('gastos/{gasto}/edit', GastoForm::class)
        ->name('gastos.edit')
        ->middleware('can:gastos.editar');

    Route::get('empresa', EmpresaForm::class)
        ->name('empresa');

    Route::get('usuarios', UsuarioIndex::class)
        ->middleware('can:usuarios.ver')
        ->name('usuarios.index');

    Route::get('roles', RolIndex::class)
        ->name('roles.index');

    // Pantallas de Configuración: cada opción es una página propia con sidebar compartido
    Route::redirect('configuracion', 'settings/empresa');

    Route::get('settings', fn () => redirect()->route('settings.empresa'))
        ->name('settings.index');

    Route::get('settings/empresa', EmpresaForm::class)
        ->name('settings.empresa')
        ->middleware('can:empresa.gestionar');

    Route::get('settings/facturacion', FacturacionIndex::class)
        ->name('settings.facturacion')
        ->middleware('can:empresa.gestionar');

    Route::get('settings/impuestos', ImpuestoIndex::class)
        ->name('configuracion.impuestos')
        ->middleware('can:empresa.gestionar');

    Route::get('settings/usuarios', UsuarioIndex::class)
        ->name('settings.usuarios')
        ->middleware('can:usuarios.ver');
});

require __DIR__.'/auth.php';
