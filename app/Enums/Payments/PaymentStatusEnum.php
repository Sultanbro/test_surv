<?php
declare(strict_types=1);

namespace App\Enums\Payments;

use \App\Enums\Tariff\BaseEnum;

enum PaymentStatusEnum: string
{
    use BaseEnum;

    const STATUS_SUCCESS = 'succeeded';
    const STATUS_FAIL    = 'failed';
    const STATUS_WAITING_CAPTURE = 'waiting_for_capture';
    const STATUS_PENDING = 'pending';
    const STATUS_CANCELED = 'canceled';
    const STATUS_FAILED = 'failed';
    const STATUS_UNKNOWN = 'unknown_status';
}
