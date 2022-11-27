<?php

namespace App\Http\Controllers\Birthday;

use App\Http\Controllers\Controller;
use App\Http\Requests\Birthday\BirthdayRequest;
use App\Http\Requests\Birthday\BirthdaySendGiftRequest;
use App\Http\Resources\Birthday\BirthdayCollection;
use App\Http\Resources\Responses\JsonSuccessResponse;
use App\Service\PaginationService;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class BirthdayController extends Controller
{
    public function index(BirthdayRequest $request, PaginationService $paginationService): JsonResponse
    {
        $dateStart = Carbon::today()->format('m-d');
        $dateEnd = Carbon::today()->addMonth()->format('m-d');

        $birthdays = User::whereRaw(
            'date_format(`birthday`, \'%m-%d\') BETWEEN \'' . $dateStart . '\' AND \'' . $dateEnd . '\''
        )->oldest(\DB::raw('date_format(`birthday`, \'%m-%d\')'));

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
        $params = $request->validated();
        $today = today();
        $avansParams = $params + [
            'user_id' => \Auth::id(),
            'type' => 'avans',
            'day' =>  $today->day,
            'month' => $today->month,
            'year' => $today->year,
            'comment' =>'Аванс в виде подаренной суммы на день рождения ' . $user->full_name,
        ];
        $bonusParams = $params + [
            'user_id' => $user->id,
            'type' => 'bonus',
            'day' =>  $today->day,
            'month' => $today->month,
            'year' => $today->year,
            'comment' =>'Бонус в виде подарка на день рождения от ' . \Auth::user()->full_name,
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
