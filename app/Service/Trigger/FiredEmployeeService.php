<?php
declare(strict_types=1);

namespace App\Service\Trigger;

use App\Enums\Mailing\MailingEnum;
use App\Models\Mailing\MailingNotification;
use App\Models\Mailing\MailingNotificationSchedule;
use App\User;
use http\Exception\InvalidArgumentException;

/**
* Класс для работы с Service.
*/
class FiredEmployeeService
{
    /**
     * @param int $employeeId
     * @return bool
     */
    public function handle(int $employeeId): bool
    {
        $notification = MailingNotification::query()->where('frequency', MailingEnum::TRIGGER_FIRED)->first();

        if (!$notification)
        {
            throw new InvalidArgumentException('Вы не создали шаблонное уведомление по типу "Анкета уволенного"');
        }

        MailingNotificationSchedule::create(
            MailingEnum::USER,
            $employeeId,
            $notification?->id
        );

        return true;
    }
}