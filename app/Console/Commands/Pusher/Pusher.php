<?php

namespace App\Console\Commands\Pusher;

use App\Models\Mailing\MailingNotification;
use App\Service\Mailing\Notifiers\NotificationFactory;
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
    protected $description = 'Command notifies the marked systems';

    /**
     * Execute the console command.
     *
     * @return ?bool
     */
    public function handle(): ?bool
    {
        $notifications = MailingNotification::with('recipients')->where('status', 1)->get();

        foreach ($notifications as $notification)
        {
            $frequency = $notification->frequency;

            return $this->{$frequency}($notification);
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

        foreach ($notification->recipients as $recipient)
        {
            $notification = $this->mailingNotification()->first();
            $time = now()->toTimeString();

            if ($time == $notification->time)
            {
                foreach ($mailingSystems as $mailingSystem)
                {
                    NotificationFactory::createNotification($mailingSystem)->send($notification, $recipient);
                }
            }
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

        foreach ($notification->recipients as $recipient)
        {
            $days = json_decode($recipient->days);
            $today  = Carbon::now()->dayOfWeekIso;
            $time   = now()->toTimeString();

            if (in_array($today, $days) && $time == $notification->time)
            {
                foreach ($mailingSystems as $mailingSystem)
                {
                    NotificationFactory::createNotification($mailingSystem)->send($notification, $recipient);
                }
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

        foreach ($notification->recipients as $recipient)
        {
            $days   = json_decode($recipient->days);
            $today  = Carbon::now()->day;
            $time   = now()->toTimeString();

            if (in_array($today, $days) and $time == $notification->time)
            {
                foreach ($mailingSystems as $mailingSystem)
                {
                    NotificationFactory::createNotification($mailingSystem)->send($notification, $recipient);
                }
            }
        }
    }
}
