<?php

use App\Http\Controllers\OrderPartnerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Partner\PartnerPartnerController;
use App\Http\Livewire\CreateOrderPartner;


Route::get('/', [PartnerPartnerController::class, '__invoke'])->name('partner.dashboard');



Route::middleware(['auth'])->group(function () {


    Route::get('/orders/create', CreateOrderPartner::class)->name('create-order-partner');

    Route::get('orders/{order}/payment', [OrderPartnerController::class, 'payment'])->name('orderpartners.payment');

    Route::post('orders/{order}/files', [OrderPartnerController::class, 'files'])->name('orderpartners.files');

    Route::match(['get', 'put'],'orders/{order}/show', [OrderPartnerController::class, 'show'])->name('orderpartners.show');


    Route::get('orders', [OrderPartnerController::class, 'index_partner'])->name('orderpartners.index');

    Route::get('orders/{order}/pdf_cotizacion', [OrderPartnerController::class, 'pdf_cotizacion'])->name('orderpartners.pdf_cotizacion');

    Route::get('orders/{order}/pdf_order', [OrderPartnerController::class, 'pdf_order'])->name('orderpartners.pdf_order');

});
