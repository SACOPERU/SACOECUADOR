<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vivo\WelcomeController;




Route::get('/vivo', [WelcomeController::class, '__invoke'])->name('marca.vivo.index');


