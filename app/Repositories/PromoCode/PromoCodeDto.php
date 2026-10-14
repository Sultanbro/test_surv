<?php

namespace App\Repositories\PromoCode;

class PromoCodeDto implements FromArray
{
    public function __construct(
        public string $code,
        public string $name,
        public string $rate,
        public string $expired_at,
    )
    {
    }

    public static function fromArray(array $array): PromoCodeDto
    {
        return new static(
            $array['code'],
            $array['name'],
            $array['rate'],
            $array['expired_at']
        );
    }
}