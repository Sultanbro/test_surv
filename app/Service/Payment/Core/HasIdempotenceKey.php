<?php

namespace App\Service\Payment\Core;

trait HasIdempotenceKey
{

    private function generateIdempotenceKey(): string
    {
        return uniqid('', true);
    }
}