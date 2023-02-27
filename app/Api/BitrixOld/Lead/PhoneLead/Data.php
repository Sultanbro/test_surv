<?php

namespace App\Api\BitrixOld\PhoneLead;

class Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $phone,
    )
    {}
}