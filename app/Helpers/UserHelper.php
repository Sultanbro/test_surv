<?php

namespace App\Helpers;

use App\User;
use Carbon\Carbon;

class UserHelper
{
    public static function showFiredEmployee(
        User $user,
        int $year,
        int $month
    )
    {
        if($user->deleted_at == '0000-00-00 00:00:00' || $user->deleted_at == null) { // Проверка не уволен ли сотрудник
            return true;
        } else {

            $dt1 = Carbon::parse($user->deleted_at); // День увольнения
            $dt2 = Carbon::create($year, $month, 30, 0, 0, 0); // Выбранный период

            if($dt1 >= $dt2) {
                if(count($user->fines) != 0) { // Проверка есть ли хоть одна fine user-a
                    return true;
                }
            } else if ($dt1->month == $dt2->month && $dt1->year == $dt2->year) { // Проверка совпадают ли месяцы
                return true;
            }
        }
    }
}