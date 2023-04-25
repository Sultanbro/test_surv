<?php

namespace App\Models\Mailing;

use App\Service\Mailing\Types\NotifyManager;
use App\Service\Mailing\Types\PositionNotify;
use App\Service\Mailing\Types\ProfileGroupNotify;
use App\Service\Mailing\Types\UserNotify;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Mailing
{
    /**
     * @param string $name
     * @param string $title
     * @param array $typeOfMailing
     * @param string $frequency
     * @param string $time
     * @return Model
     */
    public function createNotification(
        string $name,
        string $title,
        array $typeOfMailing,
        string $frequency,
        string $time
    ): Model
    {
        return MailingNotification::query()->create([
            'name'              => $name,
            'title'             => $title,
            'type_of_mailing'   => json_encode($typeOfMailing),
            'frequency'         => $frequency,
            'time'              => $time,
            'created_by'        => \Auth::id() ?? 5
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

    /**
     * @return Collection|null
     */
    public function fetchNotifications(): ?Collection
    {
        return MailingNotification::with('schedules')->get();
    }

    /**
     * @param int $ownerId
     * @param int $id
     * @return bool
     */
    public function isOwner(
        int $id,
        int $ownerId
    ): bool
    {
        return MailingNotification::query()->where('id', $id)->where('created_by', $ownerId)->exists();
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteNotification(
        int $id
    ): bool
    {
        return MailingNotification::query()->where('id', $id)->delete();
    }
}