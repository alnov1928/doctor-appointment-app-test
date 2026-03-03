<?php

use Illuminate\Support\Facades\Route;

// Redirigir la página principal al panel de administración
Route::redirect('/','/admin');
//Route::get('/', function () {
    //return view('welcome');
//});

// Rutas protegidas que requieren autenticación
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Página principal del dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
