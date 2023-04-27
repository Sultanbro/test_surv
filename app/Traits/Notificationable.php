<?php

namespace App\Traits;

use App\Enums\Mailing\MailingEnum;
use App\Models\Mailing\MailingNotification;
use App\Models\Mailing\MailingNotificationSchedule;
use App\ProfileGroup;
use App\Service\Department\UserService;
use App\UserNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

trait Notificationable
{
    /**
     * Метод отвечает за ежемесячное уведомление.
     *
     * @return void
     */
    public function monthly(): void
    {
        $notification = $this->mailingNotification()->first();
        $days = json_decode((string)$this->days);
        $today = Carbon::now()->day;

        if (in_array($today, $days))
        {
            $this->mailing($notification, $this);
        }
    }

    /**
     * Метод отвечает за еженедельное уведомление.
     *
     * @return void
     */
    public function weekly(): void
    {
        $notification = $this->mailingNotification()->first();
        $days = json_decode((string)$this->days);
        $today = Carbon::now()->dayOfWeekIso;

        if (in_array($today, $days))
        {
            $this->mailing($notification, $this);
        }
    }

    /**
     * Метод отвечает за ежедневное уведомление.
     *
     * @return void
     */
    public function daily(): void
    {
        $notification = $this->mailingNotification()->first();
        $time = now()->toTimeString();

        if ($time == $notification->time)
        {
            $this->mailing($notification, $this);
        }
    }

    /**
     * @param Model<MailingNotification> $notification
     * @param Model<MailingNotificationSchedule> $schedule
     * @return bool
     */
    public function mailing(Model $notification, Model $schedule): bool
    {
        if ($schedule->notificationable_type == MailingEnum::USER)
        {
            UserNotification::createNotification($notification->name, $notification->title, $schedule->notificationable_id);
        }

        if ($schedule->notificationable_type == MailingEnum::GROUP)
        {
            $userIds = $schedule->notificationable->activeUsers()->pluck('id')->toArray();

            foreach ($userIds as $userId)
            {
                UserNotification::createNotification($notification->name, $notification->title, $userId);
            }
        }

        if ($schedule->notificationable_type == MailingEnum::POSITION)
        {
            $userIds = $schedule->notificationable->users()->pluck('id')->toArray();

            foreach ($userIds as $userId)
            {
                UserNotification::createNotification($notification->name, $notification->title, $userId);
            }
        }

        return true;
    }
}