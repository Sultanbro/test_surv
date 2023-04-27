<?php

namespace App\Console\Commands\Pusher;

use App\Models\Mailing\MailingNotification;
use App\Service\Mailing\Notifiers\NotificationFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
     * @return int
     */
    public function handle()
    {
        $notifications = MailingNotification::with('schedules')->where('frequency', 'monthly')->get();

        foreach ($notifications as $notification)
        {
            $mailingSystems = json_decode($notification->type_of_mailing);

            foreach ($mailingSystems as $mailingSystem)
            {
                NotificationFactory::createNotification($mailingSystem)->send($notification);
            }
        }
    }
}
