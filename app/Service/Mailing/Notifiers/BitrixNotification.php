<?php

namespace App\Service\Mailing\Notifiers;

use App\Enums\Mailing\MailingEnum;
use App\Facade\MailingFacade;
use App\Models\Mailing\MailingNotification;
use App\Models\Mailing\MailingNotificationSchedule;
use App\ProfileGroup;
use App\Service\Integrations\BitrixIntegrationService;
use App\User;
use http\Exception\InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\HttpClientException;

class BitrixNotification implements Notification
{
    private BitrixIntegrationService $service;

    public function __construct()
    {
        $this->service = new BitrixIntegrationService();
    }

    /**
     * @param Model $notification
     * @param string $message
     * @return ?bool
     * @throws HttpClientException
     */
    public function send(Model $notification, string $message = ''): ?bool
    {
        $recipients = MailingFacade::getRecipients($notification->id)
            ->withWhereHas('user_description', fn($user) => $user->where('bitrix_id', '!=', 0))
            ->get();

        foreach ($recipients as $recipient)
        {
            $this->service->addNotification($recipient->user_description->bitrix_id, $message);
        }

        return true;
    }
}