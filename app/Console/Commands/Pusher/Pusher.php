<?php

namespace App\Console\Commands\Pusher;

use App\Enums\Mailing\MailingEnum;
use App\Models\Mailing\MailingNotification;
use App\ProfileGroup;
use App\Service\Mailing\Notifiers\NotificationFactory;
use App\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

class Pusher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:pusher';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Команда уведомляет пользователей через JT или другой сервис с которым мы интегрированы.';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
        $notifications = MailingNotification::with('recipients')
            ->whereIn('frequency', [MailingEnum::DAILY, MailingEnum::WEEKLY, MailingEnum::MONTHLY])
            ->where('status', 1)->get();

        foreach ($notifications as $notification)
        {
            $frequency = $notification->frequency;

            if (!method_exists($this, $frequency))
            {
                throw new Exception("Method $frequency does not exist");
            }

            $this->{$frequency}($notification);
        }
    }

    /**
     * @param MailingNotification $notification
     * @return void
     * @throws Throwable
     */
    private function daily(
        MailingNotification $notification
    ): void
    {
        $mailingSystems = json_decode($notification->type_of_mailing);
        $recipientIds = $this->getUserIds($notification->recipients);
        $recipients = User::query()->whereIn('id', $recipientIds)->get();

        foreach ($mailingSystems as $mailingSystem)
        {
            NotificationFactory::createNotification($mailingSystem)->send($notification, $notification->title,$recipients);
        }
    }

    /**
     * @param MailingNotification $notification
     * @return void
     * @throws Throwable
     */
    private function weekly(
        MailingNotification $notification
    ): void
    {
        $mailingSystems = json_decode($notification->type_of_mailing);
        $days   =  json_decode($notification->days);
        $today  = Carbon::now()->dayOfWeekIso;
        $recipientIds = $this->getUserIds($notification->recipients);
        $recipients = User::query()->whereIn('id', $recipientIds)->get();

        if (in_array($today, $days))
        {
            foreach ($mailingSystems as $mailingSystem)
            {
                NotificationFactory::createNotification($mailingSystem)->send($notification, $notification->title,$recipients);
            }
        }
    }

    /**
     * @param MailingNotification $notification
     * @return void
     * @throws Throwable
     */
    private function monthly(
        MailingNotification $notification
    ): void
    {
        $mailingSystems = json_decode($notification->type_of_mailing);
        $days   =  json_decode($notification->days);
        $today  = Carbon::now()->day;
        $recipientIds = $this->getUserIds($notification->recipients);
        $recipients = User::query()->whereIn('id', $recipientIds)->get();

        if (in_array($today, $days))
        {
            foreach ($mailingSystems as $mailingSystem)
            {
                NotificationFactory::createNotification($mailingSystem)->send($notification, $notification->title,$recipients);
            }
        }

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
                $userIds = User::where('position_id', $item->notificationable_id)->pluck('id')->toArray();
                $employeeIds = array_merge($employeeIds, $userIds);
            }

        }
        return array_unique($employeeIds);
    }
}
