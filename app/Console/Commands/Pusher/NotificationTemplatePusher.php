<?php

namespace App\Console\Commands\Pusher;

use App\Enums\Mailing\MailingEnum;
use App\Facade\MailingFacade;
use App\Models\Mailing\Mailing;
use App\Models\Mailing\MailingNotification;
use App\Service\Mailing\Notifiers\NotificationFactory;
use Exception;
use http\Exception\InvalidArgumentException;
use Illuminate\Console\Command;
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
        $notifications = MailingNotification::getTemplates()
            ->whereIn('frequency', [MailingEnum::TRIGGER_FIRED, MailingEnum::TRIGGER_COACH_ASSESSMENT, MailingEnum::TRIGGER_MANAGER_ASSESSMENT])
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
    private function fired_employee_pusher(
        MailingNotification $notification
    ): void
    {
        $user = MailingFacade::getRecipients($notification->id)->first();
        $mailings = $notification->mailings();

        if (!$user->isFired())
        {
            throw new Exception("$user->full_name is not fired");
        }

        $link       = "https://bp.jobtron.org/";
        $message    = $notification->title . ' <br> ';
        $message   .= $link;

        foreach ($mailings as $mailing)
        {
            NotificationFactory::createNotification($mailing)->send($notification, $message);
        }
    }
}
