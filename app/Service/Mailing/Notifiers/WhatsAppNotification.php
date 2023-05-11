<?php

namespace App\Service\Mailing\Notifiers;

use App\Classes\Helpers\Phone;
use App\Enums\Mailing\MailingEnum;
use App\Facade\MailingFacade;
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

    /**
     * @param Model $notification
     * @param string $message
     * @return bool|null
     * @throws Exception
     */
    public function send(Model $notification, string $message = ''): ?bool
    {
        $recipients = MailingFacade::getRecipients($notification->id)
            ->where('phone', '!=', '')->get();

        foreach ($recipients as $recipient)
        {
            $this->sendNotification($recipient, $message);
        }

        return true;
    }

    /**
     * @param User $user
     * @param string $message
     * @return void
     * @throws Exception
     */
    private function sendNotification(
        User $user,
        string $message
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
            'text'      => $message,
            'chatType'  => 'whatsapp',
        ]);

        if (!$response->successful())
        {
            throw new HttpClientException($response->body());
        }
    }
}