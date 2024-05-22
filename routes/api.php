<?php

use App\Http\Controllers\Tariff\TariffController;
use Illuminate\Support\Facades\Route;

Route::get('/tariff/get', [TariffController::class, 'get'])->name('lending.tariffs.get');