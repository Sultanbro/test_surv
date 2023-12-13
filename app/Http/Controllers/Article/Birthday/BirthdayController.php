<?php

namespace App\Http\Controllers\Article\Birthday;

use App\Http\Controllers\Controller;
use App\Http\Requests\Birthday\BirthdayRequest;
use App\Http\Requests\Birthday\BirthdaySendGiftRequest;
use App\Http\Resources\Birthday\BirthdayCollection;
use App\Http\Resources\Responses\JsonSuccessResponse;
use App\Salary;
use App\Service\PaginationService;
use App\Service\Salary\SalaryService;
use App\User;
use Illuminate\Http\JsonResponse;

class BirthdayController extends Controller
{
    public function index(BirthdayRequest $request, PaginationService $paginationService): JsonResponse
    {
        $date = now();

        $birthdays =User::query()
            ->whereHas('description', function ($query)  {
                    $query->where('is_trainee',0);
            })
            ->where(function ($query) use ($date) {
                $query->whereMonth('birthday', '=', $date->month)
                    ->whereDay('birthday', '>=', $date->day);
            })
            ->whereNotNull('birthday')
            ->oldest(\DB::raw('date_format(`birthday`, \'%m-%d\')'));

        return response()->json(
            new JsonSuccessResponse(
                __('model/birthday.index'),
                (new BirthdayCollection(
                    $paginationService->paginate($birthdays, $request->getPagination())
                ))->toArray($request)
            )
        );
    }

    /**
     * @throws \Exception
     */
    public function sendGift(BirthdaySendGiftRequest $request, User $user): JsonResponse
    {
        $today = today();
        $giftAmount = $request->input('amount') ?? 0;
        // Аванс для отправитель
        $prepaymentOfGiver = Salary::query()->where('user_id', \Auth::id())
            ->whereDate('date', $today)
            ->first()
            ->paid + $giftAmount;

        // Бонус для получатель
        $bonusForRecipient = Salary::query()->where('user_id',$user->id)
            ->whereDate('date', $today)
            ->first()
            ->bonus + $giftAmount;

        SalaryService::updateSalary($today, 'avans', $prepaymentOfGiver, $giftAmount, 'Аванс в виде подаренной суммы на день рождения', \Auth::user());
        SalaryService::updateSalary($today, 'bonus', $bonusForRecipient, $giftAmount, 'Бонус в виде подарка на день рождения', $user);


        return response()->json([
            'success' => true
        ]);
    }
}
