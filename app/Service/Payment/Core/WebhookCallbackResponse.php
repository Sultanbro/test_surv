<?php

namespace App\Service\Payment\Core;

use JsonSerializable;

class WebhookCallbackResponse implements JsonSerializable
{
    public function __construct(private readonly array $answer)
    {
    }

    public function jsonSerialize(): array
    {
        return $this->answer;
    }
}