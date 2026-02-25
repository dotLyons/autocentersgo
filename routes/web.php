<?php

use App\Livewire\CRM\Index as CRMIndex;
use App\Livewire\CRM\CreateEdit as CRMCreateEdit;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Removed welcome view

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // La ruta principal será POS (Caja)
    Route::get('/', \App\Livewire\POS\Index::class)->name('dashboard'); // Se deja el nombre 'dashboard' por conveniencia con el menú y autenticaciones

    // CRM Bounded Context Routes
    Route::prefix('crm')->name('crm.')->group(function () {
        Route::get('/', CRMIndex::class)->name('index');
        Route::get('/create', CRMCreateEdit::class)->name('create');
        Route::get('/{id}/edit', CRMCreateEdit::class)->name('edit');
        Route::get('/{id}/perfil', \App\Livewire\CRM\Show::class)->name('show');
    });

    // Ventas (Sales) Context
    Route::prefix('ventas')->name('ventas.')->group(function () {
        Route::get('/', \App\Livewire\Ventas\Index::class)->name('index');
        Route::get('/nueva', \App\Livewire\Ventas\Create::class)->name('create');
    });

    // POS Bounded Context Routes
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/caja', \App\Livewire\POS\Index::class)->name('index');
    });

    // Vehicles Bounded Context Routes
    Route::prefix('vehicles')->name('vehicles.')->group(function () {
        Route::get('/', \App\Livewire\Vehicles\Index::class)->name('index');
        Route::get('/create', \App\Livewire\Vehicles\CreateEdit::class)->name('create');
        Route::get('/{id}/edit', \App\Livewire\Vehicles\CreateEdit::class)->name('edit');
        Route::get('/{id}/perfil', \App\Livewire\Vehicles\Show::class)->name('show');
    });
});
