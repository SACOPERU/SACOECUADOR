<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Samsung\WelcomeController;




Route::get('/samsung', [WelcomeController::class, '__invoke'])->name('marca.samsung.index');


