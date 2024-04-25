<?php

use App\Http\Controllers\OrderMistore\OrderMistoreController;
use Illuminate\Support\Facades\Route;



// Ruta para mostrar el formulario
Route::get('/create-client', [OrderMistoreController::class, 'create'])->name('mistore.create-client');

// Ruta para procesar el formulario y almacenar en la base de datos
Route::post('/store-client', [OrderMistoreController::class, 'store'])->name('mistore.store-client');

// Ruta para la vista de éxito
Route::get('/success', [OrderMistoreController::class, 'success'])->name('mistore.success');

//Route::get('/show-client',OrderMistoreController::class, 'show')->name('mistore.show-client');


