<?php

use App\Http\Controllers\Invoice\WalletOneInvoiceController;
use App\Http\Controllers\Tariff\TariffController;
use Illuminate\Support\Facades\Route;

Route::get('/tariff/get', [TariffController::class, 'get'])->name('lending.tariffs.get');
Route::post('/payments/invoices/wallet-one', WalletOneInvoiceController::class);