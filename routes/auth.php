<?php

use App\Http\Controllers\Auth\LogoutController;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\ResetPassword;
use Illuminate\Support\Facades\Route;

// Logout por POST: obligar verbo HTTP impide logout forzado por CSRF
Route::post('logout', LogoutController::class)
    ->middleware('auth')
    ->name('logout');

Route::middleware('guest')->group(function () {
    Route::get('login', Login::class)
        ->name('login');

    Route::get('forgot-password', ForgotPassword::class)
        ->name('password.request');

    Route::get('reset-password/{token}', ResetPassword::class)
        ->name('password.reset');
});

// Sin registro público: la ruta /register no existe por diseño
