<?php

namespace App\Service\Payment\Core\Base;

trait HasIdempotenceKey
{

    private function generateIdempotenceKey(): string
    {
        return uniqid('', true);
    }
}