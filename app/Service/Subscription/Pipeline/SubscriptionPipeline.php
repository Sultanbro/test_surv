<?php

namespace App\Service\Subscription\Pipeline;

use App\DTO\Payment\CreateInvoiceDTO;
use App\DTO\Payment\NewSubscriptionDTO;
use App\Facade\Payment\Gateway;
use App\Jobs\ProcessCreatePaymentInvoiceLead;
use App\Models\CentralUser;
use App\Models\Tariff\TariffSubscription;
use App\Service\Payment\Core\Customer\CustomerDto;
use App\Service\Payment\Core\Invoice\Invoice;
use App\Service\Subscription\CanCalculateTariffPrice;
use App\Service\Subscription\SubscribeService;
use Exception;

class SubscriptionPipeline
{
    use CanCalculateTariffPrice;
    private CustomerDto $customer;
    private TariffSubscription $subscription;
    private Invoice $invoice;
    private ?CentralUser $user;

    public function __construct(private readonly NewSubscriptionDTO $data)
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
        $this->user = CentralUser::fromAuthUser();
        $this->customer = CentralUser::fromAuthUser()->customer();
    }

    /**
     * @throws Exception
     */
    private function createPaymentInvoice(): void
    {
        $invoice = new CreateInvoiceDTO(
            $this->data->currency,
            $this->getPrice($this->data)
        );

        $this->invoice = Gateway::provider($this->data->provider)->createNewInvoice($invoice, $this->customer);
    }

    /**
     * @throws Exception
     */
    private function subscribeToTariff(): void
    {
        $service = new SubscribeService($this->data, $this->invoice->getTransaction());
        $this->subscription = $service->subscribe();
    }

    private function createLeadInCrm(): void
    {
        ProcessCreatePaymentInvoiceLead::dispatch($this->user, $this->subscription)
            ->onConnection('sync');
    }
}