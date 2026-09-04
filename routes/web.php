<?php

use App\Livewire\Clientes\ClienteIndex;
use App\Livewire\Configuracion\EmpresaForm;
use App\Livewire\Dashboard;
use App\Livewire\Facturas\FacturaForm;
use App\Livewire\Facturas\FacturaIndex;
use App\Livewire\Facturas\FacturaShow;
use App\Livewire\Gastos\GastoForm;
use App\Livewire\Gastos\GastoIndex;
use App\Livewire\Gastos\GastoShow;
use App\Livewire\Productos\ProductoIndex;
use App\Livewire\Profile;

use App\Models\Empresa;
use App\Models\Factura;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

use App\Http\Middleware\PreventBackHistory;
use App\Livewire\Clientes\ClienteForm;
use App\Livewire\Clientes\ClienteShow;
use App\Livewire\Productos\ProductoForm;

Route::middleware(['auth', PreventBackHistory::class])->group(function () {
    Route::get('dashboard', Dashboard::class)
        ->middleware(['verified'])
        ->name('dashboard');

    Route::get('/configuracion/empresa', EmpresaForm::class)
        ->name('empresa.editar')
        ->middleware('can:empresa.gestionar');

    Route::get('profile', Profile::class)
        ->name('profile');

    Route::get('clientes', ClienteIndex::class)
        ->middleware('can:clientes.ver')
        ->name('clientes');

    Route::get('clientes/create', ClienteForm::class)
        ->middleware('can:clientes.gestionar')
        ->name('clientes.create');

    Route::get('clientes/{cliente}/edit', ClienteForm::class)
        ->middleware('can:clientes.gestionar')
        ->name('clientes.edit');

    Route::get('clientes/{cliente}', ClienteShow::class)
        ->middleware('can:clientes.ver')
        ->name('clientes.show');

    Route::get('productos', ProductoIndex::class)
        ->middleware('can:productos.ver')
        ->name('productos.index');

    Route::get('productos/create', ProductoForm::class)
        ->middleware('can:productos.gestionar')
        ->name('productos.create');

    Route::get('productos/{producto}/edit', ProductoForm::class)
        ->middleware('can:productos.gestionar')
        ->name('productos.edit');

    Route::get('facturas', FacturaIndex::class)
        ->name('facturas');

    Route::get('facturas/crear', FacturaForm::class)
        ->middleware('can:facturas.gestionar')
        ->name('facturas.crear');

    Route::get('facturas/{factura}', FacturaShow::class)
        ->middleware('can:facturas.ver')
        ->name('facturas.show');

    Route::get('facturas/{factura}/pdf', function (Factura $factura) {
        if (! auth()->user()->can('facturas.ver')) {
            abort(403);
        }
        $empresa = Empresa::actual();
        $factura->load(['cliente', 'vendedor', 'items']);
        $pdf = Pdf::loadView('pdf.factura', compact('factura', 'empresa'));

        if (request()->has('print')) {
            return $pdf->stream('factura-'.$factura->numero_factura.'.pdf');
        }

        return $pdf->download('factura-'.$factura->numero_factura.'.pdf');
    })->name('facturas.pdf');



    Route::get('gastos', GastoIndex::class)
        ->name('gastos')
        ->middleware('can:gastos.ver');

    Route::get('gastos/crear', GastoForm::class)
        ->name('gastos.crear')
        ->middleware('can:gastos.gestionar');

    Route::get('gastos/{gasto}', GastoShow::class)
        ->name('gastos.show')
        ->middleware('can:gastos.ver');

    Route::get('empresa', EmpresaForm::class)
        ->name('empresa');

    Route::get('configuracion', \App\Livewire\Configuracion\ConfiguracionIndex::class)
        ->name('configuracion.index');

    Route::get('configuracion/facturacion', \App\Livewire\Configuracion\FacturacionIndex::class)
        ->name('configuracion.facturacion');

    Route::get('usuarios', \App\Livewire\Roles\UsuarioIndex::class)
        ->name('usuarios.index');

    Route::get('roles', \App\Livewire\Roles\RolIndex::class)
        ->name('roles.index');
});

require __DIR__.'/auth.php';
