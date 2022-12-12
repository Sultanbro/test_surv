<?php

namespace App\Http\Controllers\Birthday;

use App\Http\Controllers\Controller;
use App\Http\Requests\Birthday\BirthdayRequest;
use App\Http\Requests\Birthday\BirthdaySendGiftRequest;
use App\Http\Resources\Birthday\BirthdayCollection;
use App\Http\Resources\Responses\JsonSuccessResponse;
use App\Salary;
use App\Service\PaginationService;
use App\User;
use Carbon\Carbon;
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

    public function sendGift(BirthdaySendGiftRequest $request, User $user): JsonResponse
    {
        $today = today();
        $avans =   Salary::where('user_id', \Auth::id())
            ->whereYear('date', $today->year)
            ->whereMonth('date', $today->month)
            ->whereDay('date', $today->day)
            ->first()
            ->paid + $request->input('amount') ?? 0;
        $bonus = Salary::where('user_id',$user->id)
            ->whereYear('date', $today->year)
            ->whereMonth('date', $today->month)
            ->whereDay('date', $today->day)
            ->first()
            ->bonus  + $request->input('amount') ?? 0;

        $avansParams = [
            'user_id' => \Auth::id(),
            'type' => 'avans',
            'day' =>  $today->day,
            'month' => $today->month,
            'year' => $today->year,
                'amount' =>  $avans,
            'comment' =>'Аванс в виде подаренной суммы на день рождения',
        ];
        $bonusParams =  [
            'user_id' => $user->id,
            'type' => 'bonus',
            'day' =>  $today->day,
            'month' => $today->month,
            'year' => $today->year,
                'amount' =>  $bonus,
                'comment' =>'Бонус в виде подарка на день рождения',
        ];


        return response()->json(
            new JsonSuccessResponse(
                __('model/birthday.send_gift'),
                [
                    'avansData' => $avansParams,
                    'bonusData' => $bonusParams
                ]
            )
        );
    }
}
