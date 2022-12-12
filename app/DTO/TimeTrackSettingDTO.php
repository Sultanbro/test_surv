<?php

namespace App\DTO;

class TimeTrackSettingDTO
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