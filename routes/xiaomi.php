<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Xiaomi\WelcomeController;




Route::get('/xiaomi', [WelcomeController::class, '__invoke'])->name('marca.xiaomi.index');


