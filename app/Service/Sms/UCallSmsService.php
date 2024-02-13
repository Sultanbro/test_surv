<?php

namespace App\Service\Sms;

class UCallSmsService implements SmsInterface
{
    public function __construct(
        private readonly ApiClientInterface $apiClient
    )
    {
    }

    public function send(ReceiverDto $receiver, string $message): void
    {
        $params = [
            'phones' => [
                $receiver->toArray()
            ],
            'text' => $message,
            'appId' => config('services.u-call.app_id')
        ];
        $this->apiClient->post('/api/sms/add', $params);
    }
}