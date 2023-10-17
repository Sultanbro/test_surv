<?php

namespace App\Service\Referral\Core;

class RequestDto
{

    /**
     * @param string|null $name
     * @param string|null $phone
     */
    public function __construct(
        public null|string   $name
        , public null|string $phone
    )
    {
    }
}