<?php

namespace App\Http\Controllers\Invoice;

use App\Models\Invoice;
use Illuminate\Http\JsonResponse;

class InvoiceController
{
    public function __invoke(): JsonResponse
    {
        return response()->json(Invoice::query()->get());
    }
}