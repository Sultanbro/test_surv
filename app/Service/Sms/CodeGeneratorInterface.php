<?php

namespace App\Service\Sms;
interface CodeGeneratorInterface
{
    public function generate(): int;
}