<?php

namespace App\DTO;

abstract class BaseDTO
{
    abstract public function toArray(): array;
}