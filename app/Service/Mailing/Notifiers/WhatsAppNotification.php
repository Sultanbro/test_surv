<?php

namespace App\Service\Mailing\Notifiers;

use App\Classes\Helpers\Phone;
use App\Enums\Mailing\MailingEnum;
use App\Facade\MailingFacade;
use App\Jobs\WhatsAppNotificationJob;
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
    public function __construct()
    {
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
            foreach ($recipients as $key=>$recipient) {
                WhatsAppNotificationJob::dispatch($recipient,$message)->delay(now()->addMinutes($key+0.5));
            }
            return true;
        }
    }
}