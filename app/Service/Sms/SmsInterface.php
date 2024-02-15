<?php

namespace App\Service\Sms;

interface SmsInterface
{
    public function send(ReceiverDto $receiver, string $message): void;
}