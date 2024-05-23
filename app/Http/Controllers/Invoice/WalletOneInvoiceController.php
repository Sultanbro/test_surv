<?php

namespace App\Http\Controllers\Invoice;

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
        $customer = new CustomerDto(0, 'kzt', 'unknown customer',' ');
        $data = new CreateInvoiceDTO('kzt', $request->get('amount'));
        $invoice = Gateway::provider('kzt')->createInvoice($data, $customer);
//        Invoice::createFromPaymentInvoice($invoice);
        return response()->json($invoice);
    }
}