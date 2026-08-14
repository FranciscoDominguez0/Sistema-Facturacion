<?php

use App\Livewire\Clientes\ClienteIndex;
use App\Livewire\Configuracion\EmpresaForm;
use App\Livewire\Dashboard;
use App\Livewire\Facturas\FacturaIndex;
use App\Livewire\Gastos\GastoIndex;
use App\Livewire\Productos\ProductoIndex;
use App\Livewire\Profile;
use App\Livewire\Vendedores\VendedorIndex;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

use App\Http\Middleware\PreventBackHistory;
use App\Livewire\Clientes\ClienteForm;
use App\Livewire\Clientes\ClienteShow;

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
        ->middleware('can:clientes.gestionar')
        ->name('clientes.create');

    Route::get('clientes/{cliente}/edit', ClienteForm::class)
        ->middleware('can:clientes.gestionar')
        ->name('clientes.edit');

    Route::get('clientes/{cliente}', ClienteShow::class)
        ->middleware('can:clientes.ver')
        ->name('clientes.show');

    Route::get('productos', ProductoIndex::class)
        ->name('productos');

    Route::get('facturas', FacturaIndex::class)
        ->name('facturas');

    Route::get('vendedores', VendedorIndex::class)
        ->name('vendedores');

    Route::get('gastos', GastoIndex::class)
        ->name('gastos');

    Route::get('empresa', EmpresaForm::class)
        ->name('empresa');
});

require __DIR__.'/auth.php';
