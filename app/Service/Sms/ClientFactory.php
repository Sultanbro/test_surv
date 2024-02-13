<?php

namespace App\Service\Sms;

class ClientFactory
{
    public static function init(string $apiKey): UCallApiClient
    {
        return new UCallApiClient($apiKey);
    }
}