<?php

namespace App\Service\Fine;

use App\Fine;
use App\User;
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
        return Fine::query()->get();
    }

    /**
     * @param int $month
     * @param User $user
     * @param $currency_rate
     * @return array
     */
    public function getUserFines(int $month, User $user, $currency_rate): array
    {
        $userFines = UserFine::query()->where('status', UserFine::STATUS_ACTIVE)
            ->whereYear('day', date('Y'))
            ->whereMonth('day', $month)
            ->where('user_id', $user->id)
            ->get();

        $totalFines = 0;
        foreach ($userFines as $userFine) {
            $fine = Fine::find($userFine->fine_id);
            if ($fine) {
                $amount = (int)$fine->penalty_amount * $currency_rate;
                $totalFines += $amount;
                $amount = number_format($amount, 2, '.', ',');
                $userFine->name = $fine->name . '. Сумма: ' . $amount . ' ' . strtoupper($user->currency);
            } else {
                $userFine->name = 'Добавлен штраф без ID. Сообщите в тех.поддержку';
            }
        }
        $userFines = $userFines->groupBy(function ($fine) {
            return Carbon::parse($fine->day)->format('d');
        });
        return [
            'fines' => $userFines,
            'total' => $totalFines
        ];
    }
}