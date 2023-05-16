<?php

namespace App\Console\Commands\Pusher;

use App\Enums\Mailing\MailingEnum;
use App\Facade\MailingFacade;
use App\Models\Mailing\Mailing;
use App\Models\Mailing\MailingNotification;
use App\Models\Mailing\MailingNotificationSchedule;
use App\Service\Mailing\Notifiers\NotificationFactory;
use App\User;
use Carbon\Carbon;
use Exception;
use http\Exception\InvalidArgumentException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class NotificationTemplatePusher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:pusher:template';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Запустить шаблонное уведомление.';

    /**
     * Execute the console command.
     *
     * @return bool
     * @throws Exception
     */
    public function handle()
    {
        $notifications = MailingNotification::getTemplates()->isActive()
            ->whereIn('frequency', [MailingEnum::TRIGGER_MANAGER_ASSESSMENT, MailingEnum::TRIGGER_COACH_ASSESSMENT])
            ->get();

        foreach ($notifications as $notification)
        {
            $methodName = $notification->frequency . '_pusher';

            if (!method_exists($this, $methodName))
            {
                throw new InvalidArgumentException("Method $methodName does not exist");
            }

            return $this->{$methodName}($notification);
        }

        return true;
    }

    /**
     * @param MailingNotification $notification
     * @return void
     * @throws Throwable
     */
    private function manager_assessment_pusher(
        MailingNotification $notification
    ): void
    {
        $currentDay     = Carbon::now()->day;
        $lastDayOfMonth = Carbon::now()->daysInMonth;
        $daysRemaining  = $lastDayOfMonth - $currentDay;
        $mailings       = $notification->mailings();

        $link       = 'Ссылка на опрос <br>';
        $message    = $notification->title;
        $message   .= $link;

        if ($daysRemaining == 2)
        {
            foreach ($mailings as $mailing)
            {
                NotificationFactory::createNotification($mailing)->send($notification, $message);
            }
        }
    }
}
