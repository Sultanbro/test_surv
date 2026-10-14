<?php

namespace App\Api\BitrixOld\Lead\PhoneLead;

class Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $phone,
    )
    {}
}