<?php
declare(strict_types=1);

namespace App\Service\Trigger;

use App\Enums\Mailing\MailingEnum;
use App\Models\Mailing\MailingNotification;
use App\Models\Mailing\MailingNotificationSchedule;
use App\Service\Mailing\Notifiers\NotificationFactory;
use App\User;
use http\Exception\InvalidArgumentException;
use Throwable;

/**
* Класс для работы с Service.
*/
class FiredEmployeeService
{
    /**
     * @param int $employeeId
     * @return bool
     * @throws Throwable
     */
    public function handle(int $employeeId): bool
    {
        $notification = MailingNotification::getTemplates()
            ->isActive()
            ->where('frequency', MailingEnum::TRIGGER_FIRED)->first();

        if (!(bool)$notification)
        {
            throw new InvalidArgumentException('Вы не создали шаблонное уведомление по типу "Анкета уволенного"');
        }

        $mailings = $notification?->mailings();
        $recipient = User::withTrashed()->where('id', $employeeId)->get();

        $link       = "https://bp.jobtron.org/";
        $message    = $notification->title . ' <br> ';
        $message   .= $link;

        foreach ($mailings as $mailing)
        {
            NotificationFactory::createNotification($mailing)->send($notification, $message, $recipient);
        }

        return true;
    }
}