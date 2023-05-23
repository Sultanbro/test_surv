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
use Illuminate\Support\Collection;

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
     * @param Collection|null $recipients
     * @return ?bool
     * @throws HttpClientException
     */
    public function send(Model $notification, string $message = '', Collection $recipients = null): ?bool
    {
        $recipientIds = $recipients ?? MailingFacade::getRecipients($notification->id);
        $recipients   = User::query()->whereIn('id', $recipientIds->pluck('id')->toArray())
            ->withWhereHas('user_description', fn($user) => $user->where('bitrix_id', '!=', 0))->get();

        foreach ($recipients as $recipient)
        {
            $this->service->addNotification($recipient->user_description->bitrix_id, $message);
        }

        return true;
    }
}