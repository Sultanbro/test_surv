<?php
declare(strict_types=1);

namespace App\Service\Trigger;

use App\Enums\Mailing\MailingEnum;
use App\Models\Mailing\MailingNotification;
use App\ProfileGroup;
use App\Service\Mailing\Notifiers\NotificationFactory;
use App\User;
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
            ->where('frequency', MailingEnum::TRIGGER_FIRED_WITHOUT_POLL)->first();

        if (!$notification)
        {
            throw new \Exception('Вы не создали шаблонное уведомление по типу "Уволен сотрудник"');
        }

        $mailings = $notification->mailings();
        $user = User::withTrashed()->where('id', $employeeId)->first();

        $message    = $notification->title . ' <br> ' . $user->name . " " . $user->last_name;

        $recipientIds = $this->getUserIds($notification->recipients);
        $recipients = User::query()->whereIn('id', $recipientIds)->get();

        foreach ($mailings as $mailing)
        {
            NotificationFactory::createNotification($mailing)->send($notification, $message, $recipients);
        }

        return true;
    }

    /**
     * Get employee ids which should notified by using notificationable_type and notificationable_id
     */
    private function getUserIds($recipients)
    {
        $employeeIds = [];

        foreach ($recipients as $item) {

            if($item->notificationable_type == 'App\\User') {
                $employeeIds[] = $item->notificationable_id;
            }
            elseif($item->notificationable_type == 'App\\ProfileGroup') {
                $userIds = ProfileGroup::getById($item->notificationable_id)->activeUsers()->pluck('user_id')->toArray();
                $employeeIds = array_merge($employeeIds, $userIds);
            }
            elseif($item->notificationable_type == 'App\\Position') {
                $userIds = User::query()->where('position_id', $item->notificationable_id)->pluck('id')->toArray();
                $employeeIds = array_merge($employeeIds, $userIds);
            }

        }
        return array_unique($employeeIds);
    }
}
