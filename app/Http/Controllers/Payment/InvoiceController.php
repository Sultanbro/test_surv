<?php

namespace App\Http\Controllers\Payment;

use App\Http\Requests\Payment\NewInvoiceRequest;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;

class InvoiceController
{
    public function list(): JsonResponse
    {
        return response()->json(Invoice::query()->get());
    }

    public function store(NewInvoiceRequest $request): JsonResponse
    {
        $data = $request->validated();

        Invoice::query()->create([
            'payer_name' => $data['payer_name'],
            'payer_phone' => $data['payer_phone'],
            'amount' => 0
        ]);

        return response()->json([], 201);
    }

    public function setStatusSuccess(Invoice $invoice): JsonResponse
    {
        $invoice->updateStatusToSuccess();
        return response()->json([], 201);
    }
}