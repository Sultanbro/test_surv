<?php
declare(strict_types=1);

namespace App\Enums\Payments;

enum PaymentStatusEnum: string
{
    const STATUS_SUCCESS = 'succeeded';
    const STATUS_FAIL    = 'failed';
    const STATUS_WAITING_CAPTURE = 'waiting_for_capture';
    const STATUS_PENDING = 'pending';
    const STATUS_CANCELED = 'canceled';
    const STATUS_FAILED = 'failed';
}
