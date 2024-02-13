<?php

namespace App\Service\Sms;

interface  ApiClientInterface
{
    public function request(string $method, string $url, array $data): array;

    public function post($url, array $data): array;
}