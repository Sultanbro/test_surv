<?php

namespace App\Service\Mailing\Notifiers;

use App\Classes\Helpers\Phone;
use App\Enums\Mailing\MailingEnum;
use App\Models\Mailing\MailingNotification;
use App\Models\Mailing\MailingNotificationSchedule;
use App\ProfileGroup;
use App\User;
use Exception;
use http\Exception\InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WhatsAppNotification implements Notification
{
    /**
     * @var string
     */
    private string $token;

    public function __construct()
    {
        $this->token = config('wazzup')['token'];
    }

    public function send(Model $notification, MailingNotificationSchedule $recipient): ?bool
    {
        $type = MailingEnum::TYPES[$recipient->notificationable_type] . 'Mailing';

        if (!method_exists($this, $type))
        {
            throw new InvalidArgumentException("Method $type is not defined");
        }

        return $this->{$type}($notification, $recipient->notificationable_id);
    }

    /**
     * @param MailingNotification $notification
     * @param int $groupId
     * @return void
     * @throws HttpClientException
     * @throws Exception
     */
    private function groupMailing(MailingNotification $notification, int $groupId): void
    {
        $userIds = ProfileGroup::getById($groupId)->activeUsers()
            ->where('phone', '!=', '')
            ->withWhereHas('user_description', fn($user) => $user->where('bitrix_id', '!=', 0))
            ->get();

        foreach ($userIds as $userId)
        {
            $this->sendNotification($userId, $notification);
        }
    }

    /**
     * @param MailingNotification $notification
     * @param int $userId
     * @return void
     * @throws HttpClientException
     * @throws Exception
     */
    private function individualMailing(MailingNotification $notification, int $userId): void
    {
        $user = User::query()
            ->where('phone', '!=', '')
            ->withWhereHas('user_description', fn($user) => $user->where('bitrix_id', '!=', 0))->findOrFail($userId);

        $this->sendNotification($user, $notification);
    }

    /**
     * @param MailingNotification $notification
     * @param int $positionId
     * @return void
     * @throws HttpClientException
     * @throws Exception
     */
    private function positionMailing(MailingNotification $notification, int $positionId): void
    {
        $users = User::query()
            ->where('phone', '!=', '')
            ->where('position_id', $positionId)
            ->withWhereHas('user_description', fn($user) => $user->where('bitrix_id', '!=', 0))
            ->get();


        foreach ($users as $user)
        {
            $this->sendNotification($user, $notification);
        }
    }

    /**
     * @param User $user
     * @param MailingNotification $notification
     * @return void
     * @throws Exception
     */
    private function sendNotification(
        User $user,
        MailingNotification $notification
    ): void
    {
        $phone      = Phone::normalize($user->phone);
        $channelId  = config('wazzup')['channel_id'];

        $response = Http::withHeaders([
            "Content-Type"  => "application/json",
            "Authorization" => "Bearer $this->token"
        ])->post("https://api.wazzup24.com/v3/message", [
            'channelId' => $channelId,
            'chatId'    => $phone,
            'text'      => $notification->title,
            'chatType'  => 'whatsapp',
        ]);

        if (!$response->successful())
        {
            throw new HttpClientException($response->body());
        }
    }
}