<?php

namespace App\Service\Sms;

use App\Models\SmsCode;

class CodeGenerator implements CodeGeneratorInterface
{
    public function __construct()
    {
    }

    public function generate(): int
    {
        $codes = SmsCode::query()->pluck('id')->toArray();
        $new = fake()->numberBetween(2000, 500000);
        if (in_array($new, $codes)) {
            return $this->generate();
        }
        return $new;
    }
}