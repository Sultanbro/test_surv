<?php
declare(strict_types=1);

namespace App\Service\Payments\YooKassaConnectors;

use App\DTO\Api\DoPaymentDTO;
use App\Models\Tariff\Tariff;
use App\Service\Payments\PaymentTypeConnector;
use App\Traits\YooKassaTrait;
use App\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use naffiq\tenge\CurrencyRates;
use YooKassa\Client;

class YooKassaConnector implements PaymentTypeConnector
{
    use YooKassaTrait;

    /**
     * Делает оплату.
     *
     * @throws Exception
     */

    public function doPayment(DoPaymentDTO $dto): string
    {
        return $this->pay($dto->tariffId, $dto->extraUsersLimit, $dto->autoPayment);
    }
}