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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use stdClass;

class WhatsAppNotification implements Notification
{
    /**
     * @var string
     */
    private string $token;

    public function __construct()
    {
//        $this->token = config('wazzup')['token'];
        $this->token = "8336a1a30ab94f38bb3d77209ac5047a";
    }

    /**
     * @param Model $notification
     * @param string $message
     * @param Collection|null $recipients
     * @return bool|null
     * @throws Exception
     */
    public function send(Model $notification, string $message = '', Collection $recipients = null): ?bool
    {
        if($recipients == null)
        {
            return false;
        }else {
            $recipients = $recipients->where('phone', '!=', '');

            foreach ($recipients as $recipient) {
                $this->sendNotification($recipient, $message);
            }
            return true;
        }
    }

    /**
     * @param User|stdClass $user
     * @param string $message
     * @return void
     * @throws HttpClientException
     */
    private function sendNotification(
        User|stdClass $user,
        string $message
    ): void
    {
        $phone      = Phone::normalize($user->phone);
//        $channelId  = config('wazzup')['channel_id'];
        $channelId  = '488ad573-2d11-4f16-8271-c533ab9ad891';

        $response = Http::withHeaders([
            "Content-Type"  => "application/json",
            "Authorization" => "Bearer $this->token"
        ])
            ->timeout(10000)
            ->post("https://api.wazzup24.com/v3/message", [
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