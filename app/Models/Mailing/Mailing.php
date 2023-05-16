<?php

namespace App\Models\Mailing;

use App\Enums\Mailing\MailingEnum;
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
        return MailingNotification::with('recipients')->get();
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
     * @return Collection
     */
    public function getRecipients(
        int $templateId
    ): Collection
    {
        $schedules  = MailingNotificationSchedule::query()->where('notification_id', $templateId)->get();
        $recipients = collect();
        $users      = User::query()->withWhereHas('user_description', fn ($query) => $query->where('is_trainee', 0))
            ->orderBy('last_name', 'asc')
            ->get();

        foreach ($schedules as $schedule)
        {
            if ($schedule->notificationable_type == MailingEnum::USER)
            {
                $users = User::query()->where('id', $schedule['notificationable_id'])->get();
                $recipients = $users->merge($recipients);
            }

            if ($schedule->notificationable_type == MailingEnum::GROUP)
            {
                $groupUsers = ProfileGroup::getById($schedule['notificationable_id'])->activeUsers()->get();
                $recipients = $groupUsers->merge($recipients);
            }

            if ($schedule->notificationable_type == MailingEnum::POSITION)
            {
                $positionUsers = Position::getById($schedule['notificationable_id'])->users()->whereNull('deleted_at')->get();
                $recipients = $positionUsers->merge($recipients);
            }
        }

        return $recipients->count() > 0 ? $recipients->unique() : $users;
    }
}