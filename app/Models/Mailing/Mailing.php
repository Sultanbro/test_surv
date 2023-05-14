<?php

namespace App\Models\Mailing;

use App\Position;
use App\ProfileGroup;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

class Mailing
{
    /**
     * @param string $name
     * @param string $title
     * @param array $typeOfMailing
     * @param array $days
     * @param string $frequency
     * @param bool|null $isTemplate
     * @return Model
     */
    public function createNotification(
        string $name,
        string $title,
        array $typeOfMailing,
        array $days,
        string $frequency,
        ?bool $isTemplate
    ): Model
    {
        return MailingNotification::query()->create([
            'name'              => $name,
            'title'             => $title,
            'type_of_mailing'   => json_encode($typeOfMailing),
            'days'              => json_encode($days),
            'frequency'         => $frequency,
            'is_template'       => $isTemplate,
            'created_by'        => \Auth::id() ?? 5
        ]);
    }

    /**
     * @param int $notificationableId
     * @param string $notificationableType
     * @param int $notificationId
     * @return void
     */
    public function createSchedule(
        int $notificationableId,
        string $notificationableType,
        int $notificationId
    ): void
    {
        MailingNotificationSchedule::query()->create([
            'notificationable_id'   => $notificationableId,
            'notificationable_type' => $notificationableType,
            'notification_id'       => $notificationId
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


    /**
     * @param int $templateId
     * @return Relation|Builder
     */
    public function getRecipients(
        int $templateId
    ): Relation|Builder
    {
        $recipients = MailingNotificationSchedule::query()->where('notification_id', $templateId)->get();

        foreach ($recipients as $recipient)
        {
            switch ($recipient['notificationable_type']){
                case 'App\User';
                    return User::query()->where('id', $recipient['notificationable_id']);
                case 'App\ProfileGroup';
                    return ProfileGroup::getById($recipient['notificationable_id'])->activeUsers();
                case 'App\Position';
                    return Position::getById($recipient['notificationable_id'])->users()->whereNull('deleted_at');
            }
        }
    }
}