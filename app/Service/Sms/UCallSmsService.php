<?php

namespace App\Service\Sms;

class UCallSmsService implements SmsInterface
{
    public function __construct(
        private readonly ApiClientInterface $apiClient
    )
    {
    }

    public function send(ReceiverDto $receiver, string|int $message): void
    {
        $params = [
            'phones' => [
                $receiver->toArray()
            ],
            'text' => (string)$message,
            'appid' => config('services.u-call.app_id')
        ];
        $this->apiClient->post('/api/sms/add', $params);
    }
}