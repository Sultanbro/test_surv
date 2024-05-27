<?php

use App\Http\Controllers\Payment\InvoiceController;
use App\Http\Controllers\Tariff\TariffController;
use Illuminate\Support\Facades\Route;

Route::get('/tariff/get', [TariffController::class, 'get'])->name('lending.tariffs.get');

Route::prefix('invoices/v1')->group(function () {
    Route::get('/', [InvoiceController::class, 'list']);
    Route::post('/', [InvoiceController::class, 'store']);
    Route::post('/{invoice}/status', [InvoiceController::class, 'update']);
});