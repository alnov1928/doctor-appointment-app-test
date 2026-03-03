<?php

use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\SupportTicketController;
use Illuminate\Support\Facades\Route;


// Página principal del panel de administración
Route::get('/', function(){
    return view ('admin.dashboard');
})->name('dashboard');

// Gestión de roles y permisos
Route::resource('roles', RoleController::class);

// Gestión de usuarios del sistema
Route::resource('users', UserController::class);

// Gestión de pacientes
Route::resource('patients', PatientController::class);

// Gestión de doctores
Route::resource('doctors', DoctorController::class);

// Gestión de tickets de soporte
Route::resource('support-tickets', SupportTicketController::class);
