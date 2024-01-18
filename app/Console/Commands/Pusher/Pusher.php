<?php

namespace App\Console\Commands\Pusher;

use App\Enums\Mailing\MailingEnum;
use App\Models\Mailing\MailingNotification;
use App\Service\Mailing\Notifiers\NotificationFactory;
use App\Traits\GetUsersId;
use App\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Throwable;

class Pusher extends Command
{
    use GetUsersId;

    const WEEKLY = 'weekly';
    const MONTHLY = 'monthly';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:pusher {id?}';

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
        $services = MailingNotification::with('recipients')
            ->whereIn('frequency', [
//                MailingEnum::DAILY,
                MailingEnum::WEEKLY,
                MailingEnum::MONTHLY
            ])
            ->where('status', 1)
            ->when($this->argument('id'), fn($query) => $query->where('id', $this->argument('id')))
            ->get();
        foreach ($services as $notification) {
            $frequency = $notification->frequency;

            if (!method_exists($this, $frequency)) {
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
        $this->notify($notification);
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
        if (!$this->shouldNotifyToday($notification, self::WEEKLY)) return;
        $this->notify($notification);
    }

    /**
     * @param MailingNotification $notification
     * @return void
     * @throws Throwable
     */
    private function monthly(
        MailingNotification $notification,
    ): void
    {
        if (!$this->shouldNotifyToday($notification, self::MONTHLY)) return;
        $this->notify($notification);
    }

    function shouldNotifyToday(MailingNotification $notification, string $type): bool
    {
        return match ($type) {
            self::WEEKLY => in_array(Carbon::now()->dayOfWeekIso, json_decode($notification->days)),
            self::MONTHLY => in_array(Carbon::now()->day, json_decode($notification->days)),
            default => false,
        };
    }

    /**
     * @param MailingNotification $notification
     * @throws Throwable
     */
    private function notify(MailingNotification $notification): void
    {
        $services = [];

        $mailingSystems = json_decode($notification->type_of_mailing);
        foreach ($mailingSystems as $mailingSystem) {
            $services[] = NotificationFactory::createNotification($mailingSystem);
        }

        $recipients = User::query()->find($this->getUserIds($notification->recipients));
        foreach ($services as $service) {
            $service->send($notification, $notification->title, $recipients);
        }
    }
}
