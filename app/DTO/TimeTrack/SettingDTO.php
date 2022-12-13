<?php

namespace App\DTO\TimeTrack;

class SettingDTO
{
    public function __construct(
        public int $tab
    )
    {
    }

    public static function toArray(
        int $tab
    )
    {
        return new self($tab);
    }
}