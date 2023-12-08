<?php

namespace App\Console\Commands\Pusher;

use App\ProfileGroup;
use App\User;
use Throwable;
use Exception;
use Carbon\Carbon;
use App\Classes\Helpers\Phone;
use Illuminate\Console\Command;
use App\Enums\Mailing\MailingEnum;
use App\Models\Mailing\MailingNotification;
use http\Exception\InvalidArgumentException;
use App\Service\Mailing\Notifiers\NotificationFactory;

class NotificationTemplatePusher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:pusher:template {force?}';

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
            ->whereIn('frequency', [MailingEnum::TRIGGER_MANAGER_ASSESSMENT, MailingEnum::TRIGGER_FIRED, MailingEnum::TRIGGER_FIRED_WITHOUT_POLL])
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
     * @throws Throwable
     */
    private function fired_employee_pusher(
        MailingNotification $notification
    )
    {
        $date = Carbon::now()->subDay()->format('Y-m-d');

        $users = User::withTrashed()->whereNotNull('deleted_at')->whereDate('deleted_at',$date)->get();
        $mailings = $notification?->mailings();

        foreach ($mailings as $mailing)
        {
            $this->line("type of mailing:".$mailing);
            foreach ($users as $user)
            {
                $link       = "https://bp.jobtron.org/quiz_after_fire?phone=".Phone::normalize($user->phone);
                $this->line("Id of user:".$user->id);
                $message    = $notification?->title ."\n";
                $message   .= $link;
                $fired_user = User::withTrashed()->where('id',$user->id)->whereDate('deleted_at',$date)->get();

                NotificationFactory::createNotification($mailing)->send($notification, $message, $fired_user);
            }
        }
    }

    /**
     * @throws Throwable
     */
    private function fired_employee_without_poll_pusher(
        MailingNotification $notification
    )
    {
        $date = Carbon::now()->subDay()->format('Y-m-d');

        $users = User::withTrashed()->whereNotNull('deleted_at')->whereDate('deleted_at',$date)->get();
        $mailings = $notification?->mailings();
        $recipientIds = $this->getUserIds($notification->recipients);
        $recipients = User::query()->whereIn('id', $recipientIds)->get();

        foreach ($mailings as $mailing)
        {
            $this->line("type of mailing:".$mailing);
            foreach ($users as $user)
            {
                $this->line("Id of user:".$user->id);
                $message    = $notification?->title ."\n" . $user->name . " " . $user->last_name;

                NotificationFactory::createNotification($mailing)->send($notification, $message, $recipients);
            }
        }
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
        $recipients     = User::query()
            ->withWhereHas('user_description', fn ($query) => $query->where('is_trainee', 0))
            ->withWhereHas('group_users',fn($query) => $query->where('status','active')->where('is_head',0))
            ->orderBy('last_name', 'asc')
            ->get();

        $message    = $notification->title;
        if(tenant('id') == 'bp'){
            $link = '<br> <a href="/estimate_your_trainer" class="btn btn-primary btn-sm rounded mt-1" target="_blank">Оценить</a>';
            $message .= $link;
        }

        if ($daysRemaining == 2 || $this->argument('force') == 'true')
        {
            foreach ($mailings as $mailing)
            {
                NotificationFactory::createNotification($mailing)->send($notification, $message, $recipients);
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
                $userIds = User::query()->where('position_id', $item->notificationable_id)->pluck('id')->toArray();
                $employeeIds = array_merge($employeeIds, $userIds);
            }

        }
        return array_unique($employeeIds);
    }
}
