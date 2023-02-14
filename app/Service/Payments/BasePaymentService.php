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
     * @param DoPaymentDTO $dto
     * @return string
     * @throws Exception
     */
    public function pay(DoPaymentDTO $dto): string
    {
        $response = $this->getPaymentProvider()->doPayment($dto);
        $paymentId = $response->getId();

        if ($response->getStatus() != PaymentStatusEnum::STATUS_PENDING)
        {
            throw new Exception("При генераций платежа $paymentId произошла ошибка");
        }

        PaymentIsSuccessEvent::dispatch(
            $dto->tariffId,
            $dto->extraUsersLimit,
            $paymentId,
            PaymentEnum::YOOKASSA,
            $dto->autoPayment
        );

        return $response->getConfirmation()->getConfirmationUrl();
    }

    /**
     * @param StatusPaymentDTO $dto
     * @return bool
     * @throws Exception
     */
    public function updateStatus(StatusPaymentDTO $dto): bool
    {
        try {
            $paymentStatus = $this->getPaymentInfo($dto->paymentId)->getPaymentInfo();

            if ($paymentStatus->status != PaymentStatusEnum::STATUS_SUCCESS)
            {
                new CustomException("Оплата по платежу $dto->paymentId еще не сделана", ErrorCode::BAD_REQUEST, []);
            }

            TariffPayment::query()->update([
                'status' => 'success'
            ]);

            return true;
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}