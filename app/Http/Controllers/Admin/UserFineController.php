<?php

namespace App\Http\Controllers\Admin;

use App\Components\TelegramBot;
use App\Setting;
use App\Timetracking;
use App\TimetrackingHistory;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\UserDescription;
use App\UserFine;
use App\Fine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserFineController extends Controller
{
    /**
     * Update the resources.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        $can_delete = in_array(Auth::user()->id, [5, 11741, 157, 3423]);
        
        $ud = UserDescription::where('user_id', $request->user_id)->first();

        if($ud && $ud->is_trainee == 1) return response()->json([
                                        "message" => 'Не удалось сохранить изменения. Стажеру нельзя ставить штрафы'
                                    ]);

        $resultTransaction = false;
        $fines = Fine::pluck('id')->toArray();
        $currentUserFines = UserFine::whereDate('day', $request['date'])
            ->where('user_id', $request['user_id'])
            ->where('status', UserFine::STATUS_ACTIVE)
            ->pluck('fine_id')
            ->toArray();
        $deleteFines = array_diff($fines, $request['fines']);

      
        DB::transaction(function () use (&$resultTransaction, $request, $currentUserFines, $deleteFines, $can_delete) {
            $this->addUserFines($request, $currentUserFines, $request['comment']);

            if($can_delete) {
                $this->deleteUserFines($deleteFines, $request);
            } 

            $resultTransaction = true;
        }, 5);




        $message = $resultTransaction ? 'Данные успешно сохранены!' : 'Не удалось сохранить изменения. Попробуйте еще раз!';

        return response()->json([
            "message" => $message
        ]);
    }

    /**
     * Добавление штрафов пользователю
     *
     * @param  $request
     * @param  $currentUserFines
     * @param  $comment
     */
    protected function addUserFines($request, $currentUserFines, $comment = null)
    {
        $nowFines = $request['fines'];
        $userFineModel = new UserFine;
        $wasFines = $userFineModel->getAmountUserFines($request);

        // для сценария когда штрафы только удаляют
        if ($wasFines > count($nowFines)) {
            $result = array_diff($currentUserFines, $request['fines']);
            foreach ($result as $item) {
                $fine = Fine::find($item);
                $history = [
                    'user_id' => $request['user_id'],
                    'author' => Auth::user()->name.' '.Auth::user()->last_name,
                    'author_id' => Auth::user()->id,
                    'date' => $request['date'],
                    'description' => isset($comment) ? 'Удален штраф №'.$item." ".$fine->name.', причина: '.$comment : 'Штраф',
                    'created_at' => Carbon::now()->setTimezone(Auth::user()->timezone()),
                    'updated_at' => Carbon::now()->setTimezone(Auth::user()->timezone())
                ];
                TimetrackingHistory::create($history);
            }
        }


        foreach ($request['fines'] as $fineId) {
            if(!in_array($fineId, $currentUserFines)) {
                $data = [
                    'user_id' => $request['user_id'],
                    'fine_id' => $fineId,
                    'day' => $request['date'],
                    'note' => Null
                ];
                $fine = UserFine::where('day', '=', $request['date'])
                    ->where('user_id', '=',  $request['user_id'])
                    ->where('fine_id','=',  $fineId)
                    ->where('status','=',  2)
                    ->first();
                if (is_null($fine)) {
                    $userFineModel->addUserFine($data);
                } else {
                    UserFine::turnOnFine($request, $fineId);
                }


                $history = [
                    'user_id' => $request['user_id'],
                    'author' => Auth::user()->name.' '.Auth::user()->last_name,
                    'author_id' => Auth::user()->id,
                    'date' => $request['date'],
                    'description' => isset($comment) ? 'Штраф, причина: '.$comment : 'Штраф'
                ];
                TimetrackingHistory::create($history);

                UserFine::updateTimetracking($request);

            }
        }
    }

    /**
     * Удаление штрафов пользователя
     *
     * @param $deleteFines
     * @param $request
     */
    protected function deleteUserFines($deleteFines, $request)
    {
        foreach ($deleteFines as $fineId) {

          UserFine::turnOffFine($request, $fineId);
        }
    }




}
