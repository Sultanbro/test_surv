<?php

namespace App\Http\Controllers\Payment;

use App\Classes\Helpers\Phone;
use App\Http\Requests\Payment\NewInvoiceRequest;
use App\Jobs\ProcessCreatePracticumInvoiceLead;
use App\Models\CentralUser;
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

        $user = CentralUser::fromAuthUser();

        /** @var Invoice $invoice */
        $invoice = Invoice::query()->create([
            'payer_name' => $data['payer_name'],
            'payer_phone' => Phone::normalize($data['payer_phone']),
            'name' => $data['name'],
            'url' => $data['url'],
            'provider' => $data['provider'],
            'status' => 'pending',
        ]);

        $job = new  ProcessCreatePracticumInvoiceLead($invoice);

        dispatch($job)
            ->onConnection('sync')
            ->afterCommit();

        return response()->json([], 201);
    }

    public function setStatusSuccess(Invoice $invoice): JsonResponse
    {
        $invoice->updateStatusToSuccess();
        return response()->json([], 201);
    }
}