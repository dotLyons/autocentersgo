<?php

use App\Livewire\CajaManager;
use App\Livewire\ClientesManager;
use App\Livewire\CobradorDashboard;
use App\Livewire\CobrosCreate;
use App\Livewire\LegajoCliente;
use App\Livewire\VehiculosManager;
use App\Livewire\VentasManager;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/vehiculos', VehiculosManager::class)->name('vehiculos.index');
    Route::get('/clientes', ClientesManager::class)->name('clientes.index');
    Route::get('/ventas', VentasManager::class)->name('ventas.index');
    Route::get('/clientes/{id}/legajo', LegajoCliente::class)->name('clientes.legajo');

    Route::get('/caja', CajaManager::class)->name('caja.index');
    Route::get('/cobros/nuevo', CobrosCreate::class)->name('cobros.create');

    Route::get('/cobrar-hoy', CobradorDashboard::class)->name('cobrador.index');
});
