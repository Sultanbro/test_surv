<?php
declare(strict_types=1);

namespace App\Service\Payments\Core;

use App\Api\BitrixOld\Lead\PaymentLead;
use App\DTO\Api\PaymentDTO;
use App\Enums\ErrorCode;
use App\Enums\Payments\PaymentStatusEnum;
use App\Models\CentralUser;
use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffPayment;
use App\Support\Core\CustomException;
use Exception;

abstract class BasePaymentService
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
     * @param PaymentDTO $data
     * @param CentralUser $authUser
     * @return ConfirmationResponse
     * @throws Exception
     */
    public function pay(PaymentDTO $data, CentralUser $authUser): ConfirmationResponse
    {
        $activePayment = TariffPayment::getActivePaymentIfExist($authUser);
//        if ($activePayment) {
//            throw new Exception("activePaymentIsExist");
//        }

        $connector = $this->connector();
        $response = $connector->pay($data, $authUser);
        $paymentId = $response->getPaymentId();

        $tariff = Tariff::getTariffById($data->tariffId);
        $payment = TariffPayment::createPaymentOrFail(
            $authUser->id,
            $data->tariffId,
            $data->extraUsersLimit,
            $tariff->calculateExpireDate(),
            $paymentId,
            $data->provider
        );

        $this->createPaymentLead($authUser, $payment);

        return $response;
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

    private function createPaymentLead(
        CentralUser   $user,
        TariffPayment $payment,
    ): void
    {
        (new PaymentLead(
            $user,
            $payment,
            tenant('id'),
            null,
        ))
            ->setNeedCallback(false)
            ->publish();
    }

    abstract public function invoice(array $data): PaymentInvoice;
}