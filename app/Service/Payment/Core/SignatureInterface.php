<?php

namespace App\Service\Payment\Core;

interface SignatureInterface
{
    public function make(array $data): string;

    public function verify(string $signature, array $data): bool;
}