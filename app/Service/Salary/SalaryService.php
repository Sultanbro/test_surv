<?php

namespace App\Service\Salary;

use App\Repositories\Salary\SalaryRepository;
use App\Salary;
use App\TimetrackingHistory;
use App\User;
use App\UserNotification;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

/**
 * Класс для работы с Service.
 */
class SalaryService
{

    /**
     * @param SalaryRepository $salaryRepository
     */
    public function __construct(
        public SalaryRepository $salaryRepository
    )
    {
    }

    /**
     * @param int $month
     * @param int $year
     * @param User $user
     * @return array
     *
     * Получить бонусы пользователя в таблице salaries
     */
    public function getUserBonuses(Carbon $date, User $user): array
    {
        return $this->salaryRepository->getUserBonuses($date->month, $date->year, $user->id);

    }

    /**
     * @param int $month
     * @param int $year
     * @param User $user
     * @return array
     *
     *
     * Получить все авансы в таблице salaries
     */
    public function getUserAdvances(Carbon $date, User $user): array
    {
        return $this->salaryRepository->getUserAdvance($date->month, $date->year, $user->id);

    }

    /**
     * @throws \Exception
     */
    public static function updateSalary(Carbon $date, $type, $amount, $giftAmount, $comment, $user)
    {
        $day = $date->day;
        $date = $date->format('Y-m-d');
        if ($type == 'avans') {
            $text = 'аванс';
            $key = 'paid';
        }
        elseif ($type == 'bonus') {
            $text = 'бонус';
            $key = 'bonus';
        }
        else {
            throw new \Exception('Unexpected type salary');
        }

        $salary = Salary::query()
            ->where('user_id', $user->id)
            ->whereDate('date', $date)
            ->first();

        if ($salary) {
            $salary->comment_paid = $comment;
            $salary->$key = $amount;
            $salary->save();
        }
        else {
            Salary::query()->create([
                'user_id' => $user->id,
                'date' => $date,
                'amount' => 0,
                'comment_paid' => $comment,
                $key => $amount
            ]);
        }

        // Send notification to author
        $author = Auth::user()->last_name . ' ' . Auth::user()->name;
        UserNotification::query()->create([
            'user_id' => $user->id,
            'about_id' => $user->id,
            'title' => 'Добавлен ' . $text,
            'group' => now(),
            'message' => $author . ': ' . $comment
        ]);


        /** @var User $editor */
        $editor = Auth::user();
        TimetrackingHistory::query()->create([
            'user_id' => $user->id,
            'author_id' => $editor->id,
            'author' => $editor->last_name . ' ' . $editor->name,
            'date' => $date,
            'description' => 'Добавлен <b>' . $text . '</b> на сумму ' . $giftAmount . '<br> Комментарии: ' . $comment
        ]);
    }
}
