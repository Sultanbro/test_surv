<?php

namespace App\Service\Mailing\Notifiers;

use App\Enums\Mailing\MailingEnum;
use App\Models\Mailing\MailingNotification;
use App\Models\Mailing\MailingNotificationSchedule;
use App\Position;
use App\ProfileGroup;
use App\Service\BitrixIntegrationService;
use App\User;
use http\Exception\InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Support\Facades\Http;

class BitrixNotification implements Notification
{
    private BitrixIntegrationService $service;

    public function __construct()
    {
        $this->service = new BitrixIntegrationService();
    }

    /**
     * @param Model $notification
     * @param MailingNotificationSchedule $recipient
     * @return ?bool
     */
    public function send(Model $notification, MailingNotificationSchedule $recipient): ?bool
    {
        $recipients = $notification->recipients;

        foreach ($recipients as $recipient)
        {
            $type = MailingEnum::TYPES[$recipient->notificationable_type] . 'Mailing';

            if (!method_exists($this, $type))
            {
                throw new InvalidArgumentException("Method $type is not defined");
            }

            return $this->{$type}($notification, $recipient->notificationable_id);
        }

        return true;
    }

    /**
     * @param MailingNotification $notification
     * @param int $groupId
     * @return void
     * @throws HttpClientException
     */
    private function groupMailing(MailingNotification $notification, int $groupId): void
    {
        $userIds = ProfileGroup::getById($groupId)->activeUsers()
            ->withWhereHas('user_description', fn($user) => $user->where('bitrix_id', '!=', 0))
            ->get();

        foreach ($userIds as $userId)
        {
            $this->service->addNotification($userId->user_description->bitrix_id, $notification->title);
        }
    }

    /**
     * @param MailingNotification $notification
     * @param int $userId
     * @return void
     * @throws HttpClientException
     */
    private function individualMailing(MailingNotification $notification, int $userId): void
    {
        $user = User::query()->withWhereHas('user_description', fn($user) => $user->where('bitrix_id', '!=', 0))->findOrFail($userId);

        $this->service->addNotification($user->user_description->bitrix_id, $notification->title);
    }

    /**
     * @param MailingNotification $notification
     * @param int $positionId
     * @return void
     * @throws HttpClientException
     */
    private function positionMailing(MailingNotification $notification, int $positionId): void
    {
        $users = User::query()
            ->where('position_id', $positionId)
            ->withWhereHas('user_description', fn($user) => $user->where('bitrix_id', '!=', 0))
            ->get();


        foreach ($users as $user)
        {
            $this->service->addNotification($user->user_description->bitrix_id, $notification->title);
        }
    }
}