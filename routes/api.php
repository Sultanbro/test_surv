<?php

use App\Http\Controllers\Payment\TariffController;
use Illuminate\Support\Facades\Route;

Route::get('/tariffs/get', [TariffController::class, 'get'])->name('lending.tariffs.get');