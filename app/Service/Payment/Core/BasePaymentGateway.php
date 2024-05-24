<?php
declare(strict_types=1);

namespace App\Service\Payment\Core;

use App\DTO\Payment\CreateInvoiceDTO;
use App\Enums\ErrorCode;
use App\Enums\Payments\PaymentStatusEnum;
use App\Models\Tariff\TariffSubscription;
use App\Service\Payment\Core\Callback\Invoice;
use App\Service\Payment\Core\Callback\WebhookCallback;
use App\Service\Payment\Core\Customer\CustomerDto;
use App\Support\Core\CustomException;
use Exception;

abstract class BasePaymentGateway
{
    /**
     * @return PaymentConnector
     */
    abstract public function connector(): PaymentConnector;

    /**
     * @param string $paymentId
     * @return PaymentStatus
     */
    abstract public function info(string $paymentId): PaymentStatus;


    /**
     * @param CreateInvoiceDTO $data
     * @param CustomerDto $customer
     * @return Invoice
     */
    public function createInvoice(CreateInvoiceDTO $data, CustomerDto $customer): Invoice
    {
        return $this->connector()->createNewInvoice($data, $customer);
    }

    /**
     * @param TariffSubscription $payment
     * @return bool
     * @throws Exception
     */
    public function updateStatusByPayment(TariffSubscription $payment): bool
    {
        try {
            $paymentInfo = $this->info($payment->payment_id);
            $paymentStatus = $paymentInfo->getPaymentStatus();

            $payment->status = $paymentStatus;
            $payment->save();

            if ($paymentStatus != PaymentStatusEnum::STATUS_SUCCESS) {
                new CustomException("Оплата по платежу $payment->payment_id еще не сделана", ErrorCode::BAD_REQUEST, []);
            }

            return true;
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    abstract public function webhook(array $data): WebhookCallback;

    abstract public function name():string;
    abstract public function currency():string;
}