<?php

namespace App\Service\Payments\Pipeline;

use App\DTO\Payment\CreateInvoiceDTO;
use App\Facade\Payment\Gateway;
use App\Jobs\ProcessCreatePaymentInvoiceLead;
use App\Models\CentralUser;
use App\Models\Tariff\TariffSubscription;
use App\Service\Payments\Core\Invoice;
use Exception;

class PaymentPipeline
{
    private CentralUser $user;
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
        $this->setOwnerFromAuthUser();
        $this->createPaymentInvoice();
        $this->subscribeToTariff();
        $this->createLeadInCrm();
    }

    public function invoice(): Invoice
    {
        return $this->invoice;
    }

    private function setOwnerFromAuthUser(): void
    {
        $this->user = CentralUser::fromAuthUser();
    }

    /**
     * @throws Exception
     */
    private function createPaymentInvoice(): void
    {
        $this->invoice = Gateway::provider($this->data->provider)->invoice($this->data, $this->user);
    }

    /**
     * @throws Exception
     */
    private function subscribeToTariff(): void
    {
        $this->subscription = TariffSubscription::subscribe($this->data, $this->invoice->getPaymentToken());
    }

    private function createLeadInCrm(): void
    {
        ProcessCreatePaymentInvoiceLead::dispatch($this->user, $this->subscription)
            ->onConnection('sync');
    }
}