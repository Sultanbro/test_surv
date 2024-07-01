<?php

namespace App\Http\Controllers\Practicum;

use App\Classes\Helpers\Phone;
use App\Enums\Invoice\InvoiceType;
use App\Http\Requests\Payment\NewInvoiceRequest;
use App\Jobs\ProcessCreatePracticumInvoiceLead;
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

        /** @var Invoice $invoice */
        $invoice = Invoice::query()->create([
            'payer_name' => $data['payer_name'],
            'payer_phone' => Phone::normalize($data['payer_phone']),
            'name' => $data['name'],
            'url' => $data['url'],
            'provider' => $data['provider'],
            'status' => 'pending',
            'type' => InvoiceType::PRACTICUM
        ]);

        $job = new ProcessCreatePracticumInvoiceLead($invoice);

        dispatch($job)
            ->onConnection('sync')
            ->afterResponse();

        return response()->json([], 201);
    }

    public function setStatusSuccess(Invoice $invoice): JsonResponse
    {
        $invoice->updateStatusToSuccess();
        return response()->json([], 201);
    }
}