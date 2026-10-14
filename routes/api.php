<?php

use App\Http\Controllers\Practicum\InvoiceController;
use App\Http\Controllers\Tariff\TariffController;
use Illuminate\Support\Facades\Route;

Route::get('/tariff/get', [TariffController::class, 'get'])->name('lending.tariffs.get');

Route::prefix('/v1/invoices')->group(function () {
    Route::get('/', [InvoiceController::class, 'list']);
    Route::post('/', [InvoiceController::class, 'store']);
    Route::post('/{invoice}/status', [InvoiceController::class, 'setStatusSuccess']);
});

Route::any('/test/amocrm', function (Request $request) {
    slack(json_encode($request->all()));
});
