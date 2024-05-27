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
            'actor_name' => $data['name'],
            'actor_phone' => $data['phone'],
            'amount' => $data['amount']
        ]);

        return response()->json([], 201);
    }


    public function update(): JsonResponse
    {
        return response()->json(Invoice::query()->get());
    }
}