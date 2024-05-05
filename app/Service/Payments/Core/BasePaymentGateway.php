<?php
declare(strict_types=1);

namespace App\Service\Payments\Core;

use App\DTO\Api\NewTariffPaymentDTO;
use App\Enums\ErrorCode;
use App\Enums\Payments\PaymentStatusEnum;
use App\Jobs\ProcessCreatePaymentInvoiceLead;
use App\Models\CentralUser;
use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffPayment;
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
     * @param NewTariffPaymentDTO $data
     * @param CentralUser $authUser
     * @return ConfirmationResponse
     * @throws Exception
     */
    public function pay(NewTariffPaymentDTO $data, CentralUser $authUser): ConfirmationResponse
    {
        $connector = $this->connector();
        return $connector->pay($data, $authUser);
    }

    /**
     * @param TariffPayment $payment
     * @return bool
     * @throws Exception
     */
    public function updateStatusByPayment(TariffPayment $payment): bool
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

    abstract public function invoice(array $data): PaymentInvoice;
}