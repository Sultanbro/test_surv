<?php

namespace App\Models\Mailing;

use App\Enums\Mailing\MailingEnum;
use App\Position;
use App\ProfileGroup;
use App\User;
use Carbon\Carbon;
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
     * @param int $count
     * @return Model
     */
    public function createNotification(
        string $name,
        string $title,
        array $typeOfMailing,
        array $days,
        string $frequency,
        ?bool $isTemplate,
        int $count
    ): Model
    {
        return MailingNotification::query()->create([
            'name'              => $name,
            'title'             => $title,
            'type_of_mailing'   => json_encode($typeOfMailing),
            'days'              => json_encode($days),
            'frequency'         => $frequency,
            'is_template'       => $isTemplate,
            'count'             => $count,
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

        foreach ($schedules as $schedule)
        {
            if ($schedule->notificationable_type == MailingEnum::USER)
            {
                $users = User::withTrashed()->where('id', $schedule['notificationable_id'])->get();
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

            if ($schedule->notificationable_type == MailingEnum::ALL)
            {
                $users = \DB::table('users')
                ->whereNull('deleted_at')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                /* ->where('is_trainee', 0) */;
                $recipients = $users->merge($recipients);
            }
        }

        return $recipients->unique();
    }

    /**
     * @param array $days
     * @return array
     */
    public function daysOfMonth(
        array $days
    ): array
    {
        $daysInMonth = Carbon::now()->subMonths(3)->daysInMonth;

        $lastDays = array_filter($days, function ($day) use ($daysInMonth) {
            return $day > $daysInMonth;
        });

        if (!empty($lastDays)) {
            foreach ($lastDays as $index => $day) {
                $days[$index] = $daysInMonth;
            }
        }

        return $days;
    }

    /**
     * @param array $recipients
     * @param int $notificationId
     * @return array
     */
    public function recipients(
        array $recipients,
        int $notificationId
    ): array
    {
        foreach ($recipients as &$recipient)
        {
            $recipient['notificationable_type'] = $this->replaceType($recipient['type']);
            $recipient['notificationable_id']   = $recipient['id'];
            $recipient['notification_id']       = $notificationId;

            unset($recipient['type']);
            unset($recipient['id']);
        }

        return $recipients;
    }

    /**
     * @param $type
     * @return string
     */
    public function replaceType(
        $type
    ): string
    {
        return match ($type) {
            1 => MailingEnum::USER,
            2 => MailingEnum::GROUP,
            3 => MailingEnum::POSITION,
            4 => MailingEnum::ALL,
            default => $type,
        };
    }

    /**
     * @param array $data
     * @return void
     */
    public function insertSchedule(
        array $data
    ): void
    {
        MailingNotificationSchedule::query()->insert($data);
    }
}