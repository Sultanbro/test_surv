<?php
declare(strict_types=1);

namespace App\Service\Payment\Core\Base;

use App\DTO\Payment\CreateInvoiceDTO;
use App\Service\Payment\Core\Webhook\BaseWebhook;
use App\Service\Payment\Core\Webhook\Invoice;
use App\Service\Payment\Core\Customer\CustomerDto;

abstract class BasePaymentGateway
{
    abstract public function name():string;

    abstract public function currency():string;

    abstract public function connector(): PaymentConnector;

    abstract public function webhook(): BaseWebhook;

    /**
     * @param CreateInvoiceDTO $data
     * @param CustomerDto $customer
     * @return Invoice
     */
    public function createNewInvoice(CreateInvoiceDTO $data, CustomerDto $customer): Invoice
    {
        return $this->connector()->newInvoice($data, $customer);
    }
}