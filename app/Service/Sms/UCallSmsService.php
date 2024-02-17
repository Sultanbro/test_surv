<?php

namespace App\Service\Sms;

use App\Models\Integration\Integration;

class UCallSmsService implements SmsInterface
{
    public function __construct(
        private readonly ApiClientInterface $apiClient
    )
    {
    }

    public function send(ReceiverDto $receiver, string|int $message): array
    {
        /** @var Integration $integration */
        $integration = Integration::query()->where('reference', 'u-call')->first();
        $credentials = [];
        if ($integration) {
            $credentials = json_decode($integration->data, true);
        }
        $apiKey = $credentials['api_key'] ?? config('services.u-call.api_key');
        $appId = $credentials['app_id'] ?? config('services.u-call.app_id');
        $this->apiClient->setApiKey($apiKey);
        $params = [
            'phones' => [
                $receiver->toArray()
            ],
            'text' => (string)$message,
            'appid' => $appId
        ];

        return $this->apiClient->post('/api/sms/add', $params);
    }
}