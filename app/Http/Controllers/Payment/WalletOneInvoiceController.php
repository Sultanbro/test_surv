<?php

namespace App\Http\Controllers\Payment;

use App\DTO\Payment\CreateInvoiceDTO;
use App\Facade\Payment\Gateway;
use App\Models\Invoice;
use App\Service\Payment\Core\Customer\CustomerDto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletOneInvoiceController
{
    public function __invoke(Request $request): JsonResponse
    {
        //, 'customer@jobtron.com'
        $customer = new CustomerDto(
            0,
            'kzt',
            'unknown customer',
            'customer@jobtron.com',
            $request->get('phone')
        );

        $data = new CreateInvoiceDTO('kzt', $request->get('amount'));
        $invoice = Gateway::provider('kzt')->createNewInvoice($data, $customer);
//        Invoice::new($invoice, 'wallet1');
        return response()->json($invoice);
    }
}