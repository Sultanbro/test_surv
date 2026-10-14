<?php

namespace App\Service\Subscription\Pipeline;

use App\Classes\Helpers\Phone;
use App\DTO\Payment\CreateInvoiceDTO;
use App\DTO\Payment\NewSubscriptionDTO;
use App\Enums\Invoice\InvoiceType;
use App\Facade\Payment\Gateway;
use App\Jobs\ProcessCreatePaymentInvoiceLead;
use App\Models\CentralUser;
use App\Models\Invoice;
use App\Models\Tariff\TariffSubscription;
use App\Service\Payment\Core\Customer\CustomerDto;
use App\Service\Payment\Core\Invoice\InvoiceResponse;
use App\Service\Subscription\CanCalculateTariffPrice;
use App\Service\Subscription\SubscribeService;
use Exception;

class SubscriptionPipeline
{
    use CanCalculateTariffPrice;

    private CustomerDto $customerDTO;
    private TariffSubscription $subscription;
    private InvoiceResponse $invoiceResponse;
    private ?CentralUser $user;
    private Invoice $invoice;

    public function __construct(private readonly NewSubscriptionDTO $subscriptionDTO)
    {
    }

    /**
     * @throws Exception
     */
    public function apply(): void
    {
        $this->setCustomer();
        $this->createInvoice();
        $this->subscribeToTariff();
        $this->invoice->update([
            'payload' => [
                'subscription_id' => $this->subscription->id
            ]
        ]); // sync the subscription with invoice for feature using
        $this->createLeadInCrm();
    }

    public function invoiceResponse(): InvoiceResponse
    {
        return $this->invoiceResponse;
    }

    private function setCustomer(): void
    {
        $owner = CentralUser::fromAuthUser();
        $this->user = $owner;
        $this->customerDTO = $owner->toCustomerDTO();
    }

    /**
     * @throws Exception
     */
    private function createInvoice(): void
    {
        $invoiceDTO = new CreateInvoiceDTO(
            $this->subscriptionDTO->currency,
            $this->getPrice($this->subscriptionDTO),
            InvoiceType::NEW_SUBSCRIPTION
        );

        $this->invoiceResponse = Gateway::provider($this->subscriptionDTO->provider)->createNewInvoice($invoiceDTO, $this->customerDTO);

        $this->invoice = Invoice::create([
            'payer_name' => auth()->user()->name,
            'payer_phone' => auth()->user()->phone,
            'name' => $invoiceDTO->description,
            'url' => $this->invoiceResponse->getUrl(),
            'provider' => $this->subscriptionDTO->provider,
            'status' => 'pending',
            'type' => InvoiceType::NEW_SUBSCRIPTION
        ]);
    }

    /**
     * @throws Exception
     */
    private function subscribeToTariff(): void
    {
        $service = new SubscribeService($this->subscriptionDTO, $this->invoiceResponse->getTransaction());
        $this->subscription = $service->subscribe();
    }

    private function createLeadInCrm(): void
    {
        ProcessCreatePaymentInvoiceLead::dispatch($this->user, $this->subscription)
            ->onConnection('sync');
    }
}