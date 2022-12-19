<?php

namespace App\Service\Fines;

use App\Fine;
use App\UserFine;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
* Класс для работы с Service.
*/
class FineService
{

    /**
     * @return Collection
     */
    public function getFines(): Collection
    {
        return Fine::all();
    }

    public function getUserFines($month, $user, $currency_rate): array{
        $userFines = UserFine::where('status', UserFine::STATUS_ACTIVE)
            ->whereYear('day', date('Y'))
            ->whereMonth('day', $month)
            ->where('user_id', $user->id)
            ->get();


        $totalFines = 0;
        foreach($userFines as $fine) {
            $_fine = Fine::find($fine->fine_id);
            if($_fine) {
                $amount = (int)$_fine->penalty_amount * $currency_rate;
                $totalFines += $amount;
                $amount = number_format($amount,  2, '.', ',');
                $fine->name = $_fine->name.'. Сумма: '.  $amount .' '. strtoupper($user->currency);
            } else {
                $fine->name = 'Добавлен штраф без ID. Сообщите в тех.поддержку';
            }
        }
        $userFines = $userFines->groupBy(function($fine) {
            return Carbon::parse($fine->day)->format('d');
        });
        return [
            'fines' => $userFines,
            'total' =>$totalFines
        ];
    }
}