<?php

namespace App\Models\Mailing;

use App\Service\Mailing\Types\NotifyManager;
use App\Service\Mailing\Types\PositionNotify;
use App\Service\Mailing\Types\ProfileGroupNotify;
use App\Service\Mailing\Types\UserNotify;
use Illuminate\Database\Eloquent\Model;

class Mailing
{
    /**
     * @param string $title
     * @param array $typeOfMailing
     * @param string $frequency
     * @param string $time
     * @return Model<MailingNotification>
     */
    public function createNotification(
        string $title,
        array $typeOfMailing,
        string $frequency,
        string $time
    ): Model
    {
        return MailingNotification::query()->create([
            'title' => $title,
            'type_of_mailing' => json_encode($typeOfMailing),
            'frequency' => $frequency,
            'time' => $time
        ]);
    }

    /**
     * @param int $notificationableId
     * @param string $notificationableType
     * @param int $notificationId
     * @param array $days
     * @return void
     */
    public function createSchedule(
        int $notificationableId,
        string $notificationableType,
        int $notificationId,
        array $days
    ): void
    {

        MailingNotificationSchedule::query()->create([
            'notificationable_id'   => $notificationableId,
            'notificationable_type' => $notificationableType,
            'notification_id'       => $notificationId,
            'days'                  => json_encode($days)
        ]);

    }
}