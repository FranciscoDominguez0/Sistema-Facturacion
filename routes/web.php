<?php

use App\Livewire\Clientes\ClienteIndex;
use App\Livewire\Configuracion\EmpresaForm;
use App\Livewire\Facturas\FacturaIndex;
use App\Livewire\Gastos\GastoIndex;
use App\Livewire\Productos\ProductoIndex;
use App\Livewire\Vendedores\VendedorIndex;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

use App\Http\Middleware\PreventBackHistory;

Route::middleware(['auth', PreventBackHistory::class])->group(function () {
    Route::view('dashboard', 'dashboard')
        ->middleware(['verified'])
        ->name('dashboard');

    Route::view('profile', 'profile')
        ->name('profile');

    Route::get('clientes', ClienteIndex::class)
        ->name('clientes');

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
