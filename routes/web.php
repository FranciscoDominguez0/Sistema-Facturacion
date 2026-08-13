<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

use App\Http\Middleware\PreventBackHistory;

Route::middleware(['auth', PreventBackHistory::class])->group(function () {
    Route::view('dashboard', 'dashboard')
        ->middleware(['verified'])
        ->name('dashboard');

    Route::view('profile', 'profile')
        ->name('profile');
});

require __DIR__.'/auth.php';
