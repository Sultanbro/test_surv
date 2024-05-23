<?php

namespace App\Http\Controllers\Invoice;

use App\DTO\Payment\CreateInvoiceDTO;
use App\Facade\Payment\Gateway;
use App\Service\Payment\Core\Customer\CustomerDto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletOneInvoiceController
{
    public function __invoke(Request $request): JsonResponse
    {
        $customer = new CustomerDto(0, 'unknown customer', '374700000000', 'customer@jobtron.kz', 'kzt');
        $data = new CreateInvoiceDTO('kzt', $request->get('amount'));
        $invoice = Gateway::provider('kzt')->invoice($data, $customer);
        return response()->json($invoice);
    }
}