<?php

namespace App\Service\Subscription\Pipeline;

use App\DTO\Payment\CreateInvoiceDTO;
use App\Facade\Payment\Gateway;
use App\Jobs\ProcessCreatePaymentInvoiceLead;
use App\Models\CentralUser;
use App\Models\Tariff\TariffSubscription;
use App\Service\Payment\Core\Customer\CustomerDto;
use App\Service\Payment\Core\Invoice;
use App\Service\Subscription\SubscribeService;
use Exception;

class SubscriptionPipeline
{
    private CustomerDto $customer;
    private TariffSubscription $subscription;
    private Invoice $invoice;

    public function __construct(private readonly CreateInvoiceDTO $data)
    {
    }

    /**
     * @throws Exception
     */
    public function apply(): void
    {
        $this->setCustomer();
        $this->createPaymentInvoice();
        $this->subscribeToTariff();
        $this->createLeadInCrm();
    }

    public function invoice(): Invoice
    {
        return $this->invoice;
    }

    private function setCustomer(): void
    {
        $this->customer = CentralUser::fromAuthUser()->customer();
    }

    /**
     * @throws Exception
     */
    private function createPaymentInvoice(): void
    {
        $this->invoice = Gateway::provider($this->data->provider)->invoice($this->data, $this->customer);
    }

    /**
     * @throws Exception
     */
    private function subscribeToTariff(): void
    {
        $service = new SubscribeService($this->data, $this->invoice->getPaymentToken());
        $this->subscription = $service->subscribe();
    }

    private function createLeadInCrm(): void
    {
        ProcessCreatePaymentInvoiceLead::dispatch($this->customer, $this->subscription)
            ->onConnection('sync');
    }
}