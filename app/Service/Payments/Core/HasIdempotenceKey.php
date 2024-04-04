<?php

namespace App\Service\Payments\Core;

trait HasIdempotenceKey
{

    private function generateIdempotenceKey(): string
    {
        return uniqid('', true);
    }
}