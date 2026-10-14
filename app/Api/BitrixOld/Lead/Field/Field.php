<?php

namespace App\Api\BitrixOld\Lead\Field;


class Field
{
    public function __construct(
        public readonly string $key,
        public readonly mixed $value,
    )
    {}    
}