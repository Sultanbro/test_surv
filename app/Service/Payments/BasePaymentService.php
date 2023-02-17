<?php
declare(strict_types=1);

namespace App\Service\Payments;

use App\DTO\Api\DoPaymentDTO;
use App\DTO\Api\StatusPaymentDTO;
use App\Enums\ErrorCode;
use App\Enums\Payments\PaymentEnum;
use App\Enums\Payments\PaymentStatusEnum;
use App\Events\PaymentIsSuccessEvent;
use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffPayment;
use App\Support\Core\CustomException;
use Exception;

abstract class BasePaymentService
{
    /**
     * @return PaymentTypeConnector
     */
    abstract public function getPaymentProvider(): PaymentTypeConnector;

    /**
     * @param string $paymentId
     * @return PaymentStatus
     */
    abstract public function getPaymentInfo(string $paymentId): PaymentStatus;

    /**
     * @return AutoPayment
     */
    abstract public function autoPayment(): AutoPayment;

    /**
     * @param DoPaymentDTO $dto
     * @param int $authUserId
     * @return string
     * @throws Exception
     */
    public function pay(DoPaymentDTO $dto, int $authUserId): string
    {
        $response   = $this->getPaymentProvider()->doPayment($dto, $authUserId);
        $paymentId  = $response->getId();
        $tariff     = Tariff::getTariffById($dto->tariffId);

        if ($response->getStatus() != PaymentStatusEnum::STATUS_PENDING)
        {
            throw new Exception("При генераций платежа $paymentId произошла ошибка");
        }

        TariffPayment::createPaymentOrFail(
            $dto->tariffId,
            $dto->extraUsersLimit,
            $tariff->calculateExpireDate(),
            $response->getId(),
            PaymentEnum::YOOKASSA,
            $dto->autoPayment
        );

        return $response->getConfirmation()->getConfirmationUrl();
    }

    /**
     * @param TariffPayment $payment
     * @return bool
     * @throws Exception
     */
    public function updateStatusByPayment(TariffPayment $payment): bool
    {
        try {
            $paymentStatus = $this->getPaymentInfo($payment->payment_id)->getPaymentInfo();

            if ($paymentStatus->status != PaymentStatusEnum::STATUS_SUCCESS)
            {
                //TODO save another statuses

                new CustomException("Оплата по платежу $payment->payment_id еще не сделана", ErrorCode::BAD_REQUEST, []);
            }

            $payment->status = PaymentStatusEnum::STATUS_SUCCESS;
            $payment->save();

            return true;
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param TariffPayment $payment
     * @return void
     * @throws Exception
     */
    public function doAutoPayment(TariffPayment $payment): void
    {
        $this->autoPayment($payment)->makeAutoPayment($payment);
        $tariff = Tariff::query()->findOrFail($payment->tariff_id);

        TariffPayment::createPaymentOrFail(
            $payment->tariff_id,
            $payment->extra_user_limit,
            $tariff->calculateExpireDate(),
            $payment->payment_id,
            $payment->service_for_payment,
            (bool)$payment->auto_payment
        );
    }
}