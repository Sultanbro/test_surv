<?php
declare(strict_types=1);

namespace App\Service\Payments;

use App\Api\BitrixOld\Lead\PaymentLead;
use App\DTO\Api\DoPaymentDTO;
use App\Enums\ErrorCode;
use App\Enums\Payments\PaymentEnum;
use App\Enums\Payments\PaymentStatusEnum;
use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffPayment;
use App\Support\Core\CustomException;
use App\User;
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
     * @param User $authUser
     * @return string
     * @throws Exception
     */
    public function pay(DoPaymentDTO $dto, User $authUser): string
    {
        $activePayment = TariffPayment::getActivePaymentIfExist();

        if ($activePayment) {
            throw new Exception("activePaymentIsExist");
        }

        $response = $this->getPaymentProvider()->doPayment($dto, $authUser);
        $paymentId = $response->getId();
        $tariff = Tariff::getTariffById($dto->tariffId);

        if ($response->getStatus() != PaymentStatusEnum::STATUS_PENDING) {
            throw new Exception("При генераций платежа $paymentId произошла ошибка");
        }

        $payment = TariffPayment::createPaymentOrFail(
            $authUser->id,
            $dto->tariffId,
            $dto->extraUsersLimit,
            $tariff->calculateExpireDate(),
            $response->getId(),
            PaymentEnum::YOOKASSA,
            $dto->autoPayment
        );

        $this->createPaymentLead($authUser, $payment);

        return $response->getConfirmation()->getConfirmationUrl();
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
            $paymentInfo = $this->getPaymentInfo($payment->payment_id)->getPaymentInfo();
            $paymentStatus = $paymentInfo->status;

            if (!PaymentStatusEnum::isInEnum($paymentStatus)) {
                $paymentStatus = PaymentStatusEnum::STATUS_UNKNOWN;
            }

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

    /**
     * @param TariffPayment $payment
     * @return void
     * @throws Exception
     */
    public function doAutoPayment(TariffPayment $payment): void
    {
        $this->autoPayment()->makeAutoPayment($payment);
        /** @var Tariff $tariff */
        $tariff = Tariff::query()->findOrFail($payment->tariff_id);

        TariffPayment::createPaymentOrFail(
            $payment->owner_id,
            $payment->tariff_id,
            $payment->extra_user_limit,
            $tariff->calculateExpireDate(),
            $payment->payment_id,
            $payment->service_for_payment,
            $payment->auto_payment
        );
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
        } catch (Exception $err) {
            return; //TODO add logs
        }
    }
}