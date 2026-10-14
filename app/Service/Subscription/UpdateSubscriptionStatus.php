<?php

namespace App\Service\Subscription;

use App\Enums\ErrorCode;
use App\Enums\Payments\PaymentStatusEnum;
use App\Models\Tariff\TariffSubscription;
use App\Support\Core\CustomException;
use Exception;

class UpdateSubscriptionStatus
{

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
}