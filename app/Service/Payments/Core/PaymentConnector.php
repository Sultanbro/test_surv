<?php
declare(strict_types=1);

namespace App\Service\Payments\Core;

use App\DTO\Api\PaymentDTO;
use App\User;

interface PaymentConnector
{
    public function pay(PaymentDTO $data, User $user): ConfirmationResponse;
}