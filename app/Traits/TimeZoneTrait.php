<?php

namespace App\Traits;

use App\Setting;
use Carbon\Carbon;

trait TimeZoneTrait
{
    public function getUserTimezone(): int
    {
        return auth()->user()->timezone ?? 5;
    }

    public function getTimezoneSetting(): string
    {
        return Setting::TIMEZONES[$this->getUserTimezone()];
    }

    public function getTimezone(): string
    {
        return Carbon::now($this->getTimezoneSetting())->format('d.m.Y');
    }
}