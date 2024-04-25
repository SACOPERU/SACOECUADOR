<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SearchController;
use App\Http\Livewire\CreateOrder;
use App\Http\Livewire\ShoppingCart;
use App\Http\Controllers\PaidController;
use App\Http\Livewire\Partner\CreateOrderPartner;
use App\Http\Livewire\ComplaintForm;

Route::get('/', WelcomeController::class);


Route::get('/libro-reclamaciones', function () {
    return view('livewire.complaint');
})->name('complaint');



Route::get('search', SearchController::class)->name('search');

Route::get('categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');

Route::get('shopping-cart', ShoppingCart::class)->name('shopping-cart');

Route::middleware(['auth'])->group(function () {

    //ORDERS

    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');

    Route::get('orders/create', CreateOrder::class)->name('orders.create');

    Route::get('orders/{order}/payment', [OrderController::class, 'payment'])->name('orders.payment');

    Route::get('orders/{vista}', [OrderController::class, 'vista'])->name('orders.vista');


  	//OPENPAY

  	Route::match(['get', 'post'], 'orders/{order}/openPayPayment', [OrderController::class, 'openPayPayment'])->name('orders.pagoexitoso');

    //IZIPAY

    Route::match(['get', 'post'], '/paid/izipay', [PaidController::class, 'izipay'])->name('paid.izipay');

    Route::get('orders/{order}/show', [OrderController::class, 'show'])->name('orders.show');
});


