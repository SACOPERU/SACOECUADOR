<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Infinix\WelcomeController;




Route::get('/infinix', [WelcomeController::class, '__invoke'])->name('marca.infinix.index');


