<?php
declare(strict_types=1);

namespace App\Service\Payments\Core;

use App\DTO\Api\NewTariffPaymentDTO;
use App\Models\CentralUser;

interface PaymentConnector
{
    public function pay(NewTariffPaymentDTO $data, CentralUser $user): ConfirmationResponse;

    public function getShopKey(): string;
}