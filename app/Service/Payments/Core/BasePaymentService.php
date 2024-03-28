<?php
declare(strict_types=1);

namespace App\Service\Payments\Core;

use App\Api\BitrixOld\Lead\PaymentLead;
use App\DTO\Api\PaymentDTO;
use App\Enums\ErrorCode;
use App\Enums\Payments\PaymentStatusEnum;
use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffPayment;
use App\Support\Core\CustomException;
use App\User;
use Exception;

abstract class BasePaymentService
{
    /**
     * @return PaymentConnector
     */
    abstract public function getPaymentConnector(): PaymentConnector;

    /**
     * @param string $paymentId
     * @return PaymentStatus
     */
    abstract public function getPaymentInfo(string $paymentId): PaymentStatus;


    /**
     * @param PaymentDTO $data
     * @param User $authUser
     * @return ConfirmationResponse
     * @throws Exception
     */
    public function pay(PaymentDTO $data, User $authUser): ConfirmationResponse
    {
        $activePayment = TariffPayment::getActivePaymentIfExist();

        if ($activePayment) {
            throw new Exception("activePaymentIsExist");
        }

        $connector = $this->getPaymentConnector();
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
     * @param User $authUser
     * @return bool
     * @throws Exception
     */
    public function updateStatusByPayment(TariffPayment $payment, User $authUser): bool
    {
        try {
            $paymentInfo = $this->getPaymentInfo($payment->payment_id);
            $paymentStatus = $paymentInfo->getPaymentStatus();

            $payment->status = $paymentStatus;
            $payment->save();

            if ($paymentStatus != PaymentStatusEnum::STATUS_SUCCESS) {
                $this->createPaymentLead($authUser, $payment);
                new CustomException("Оплата по платежу $payment->payment_id еще не сделана", ErrorCode::BAD_REQUEST, []);
            }

            return true;
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    private function createPaymentLead(User $user, TariffPayment $payment): void
    {
        try {
            (new PaymentLead(
                $user,
                $payment,
                tenant('id'),
                null,
            ))
                ->setNeedCallback(false)
                ->publish();
        } catch (Exception) {
            return; //TODO add logs
        }
    }
}