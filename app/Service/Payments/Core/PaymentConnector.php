<?php
declare(strict_types=1);

namespace App\Service\Payments\Core;

use App\DTO\Api\PaymentDTO;
use App\Models\CentralUser;

interface PaymentConnector
{
    public function pay(PaymentDTO $data, CentralUser $user): ConfirmationResponse;

    public function getShopKey(): string;
}