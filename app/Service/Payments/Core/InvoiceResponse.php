<?php

namespace App\Service\Payments\Core;

use JsonSerializable;

class InvoiceResponse implements JsonSerializable
{
    public function __construct(private readonly array $answer)
    {
    }

    public function jsonSerialize(): array
    {
        return $this->answer;
    }
}