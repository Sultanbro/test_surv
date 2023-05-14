<?php

namespace App\Console\Commands\Pusher;

use App\Enums\Mailing\MailingEnum;
use App\Models\Mailing\MailingNotification;
use App\Service\Mailing\Notifiers\NotificationFactory;
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
     * @return ?bool
     * @throws Exception
     */
    public function handle(): ?bool
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

            return $this->{$frequency}($notification);
        }

        return true;
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

        foreach ($mailingSystems as $mailingSystem)
        {
            NotificationFactory::createNotification($mailingSystem)->send($notification, $notification->title);
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

        if (in_array($today, $days))
        {
            foreach ($mailingSystems as $mailingSystem)
            {
                NotificationFactory::createNotification($mailingSystem)->send($notification, $notification->title, $notification->name);
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

        if (in_array($today, $days))
        {
            foreach ($mailingSystems as $mailingSystem)
            {
                NotificationFactory::createNotification($mailingSystem)->send($notification, $notification->title);
            }
        }

    }
}
