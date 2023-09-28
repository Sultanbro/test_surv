<?php

namespace App\Helpers;

use Illuminate\Support\Carbon;

class DateHelper
{
    public static function format(Carbon $date): string
    {
        $now = Carbon::now();

        return $now->diff($date)->days > 7
            ? $date->translatedFormat('d F Y')
            : $date->diffForHumans();
    }

    public static function prepareDate(string $birthday): string
    {
        $birthday = Carbon::createFromFormat('Y-m-d H:i:s',$birthday);
        $diff = $birthday->diff(Carbon::today()->setYear($birthday->year));

        if ($diff->days === 1){
            return __('datetime.tomorrow');
        }

        if ($diff->days  === 0){
            return __('datetime.today');
        }

        return  $birthday->translatedFormat('d F');
    }
}
