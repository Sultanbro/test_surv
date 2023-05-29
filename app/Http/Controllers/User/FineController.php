<?php

namespace App\Http\Controllers\User;

use App\DTO\Fine\UpdateUserFinesDTO;
use App\TimetrackingHistory;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\UserDescription;
use App\UserFine;
use App\Fine;
use App\Http\Requests\Fine\UpdateUserFinesRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FineController extends Controller
{
    /**
     * Update the resources.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(UpdateUserFinesRequest $request)
    {
        $data = $request->toDto();

        $can_delete = Auth::user()->hasPermissionTo('fines_edit');
        
        $ud = UserDescription::where('user_id', $data->user_id)->first();

        if($ud && $ud->is_trainee == 1) return response()->json([
            "message" => 'Не удалось сохранить изменения. Стажеру нельзя ставить штрафы'
        ]);

        $fines = Fine::pluck('id')->toArray();

        $currentUserFines = UserFine::whereDate('day', $data->date)
            ->where('user_id', $data->user_id)
            ->where('status', UserFine::STATUS_ACTIVE)
            ->pluck('fine_id')
            ->toArray();

        $deleteFines = array_diff($currentUserFines, $data->fines);
        $newFines = array_diff($data->fines, $currentUserFines);
        
        $resultTransaction = false;
        DB::transaction(function () use (&$resultTransaction, $data, $newFines, $deleteFines, $can_delete) {
            $this->addUserFines($data, $newFines, $data->comment);

            if($can_delete) {
                $this->deleteUserFines($deleteFines, $data);
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
     * @param  UpdateUserFinesDTO $request
     * @param  array $currentUserFines
     * @param  string|null $comment
     */
    protected function addUserFines(UpdateUserFinesDTO $dto, array $newFines, string $comment = null)
    {
        foreach ($newFines as $fine_id) {
            $data = [
                'user_id' => $dto->user_id,
                'fine_id' => $fine_id,
                'day' => $dto->date,
                'note' => null
            ];

            $fine = UserFine::whereDate('day', $dto->date)
                ->where('user_id', $dto->user_id)
                ->where('status', UserFine::STATUS_INACTIVE)
                ->where('fine_id', $fine_id)
                ->first();
            
            if (is_null($fine)) {
                (new UserFine)->addUserFine($data);
            } else {
                UserFine::turnOnFine($dto->user_id, $fine_id, $dto->date);
            }

            $history = [
                'user_id' => $dto->user_id,
                'author' => Auth::user()->name.' '.Auth::user()->last_name,
                'author_id' => Auth::user()->id,
                'date' => $dto->date,
                'description' => isset($comment) ? 'Штраф, причина: '.$comment : 'Штраф'
            ];
            TimetrackingHistory::create($history);

            UserFine::updateTimetracking($dto->user_id, $dto->date);
        }
    }

    /**
     * Удаление штрафов пользователя
     *
     * @param array $deleteFines
     * @param UpdateUserFinesDTO $dto
     */
    protected function deleteUserFines($deleteFines, UpdateUserFinesDTO $dto)
    {
        foreach ($deleteFines as $fine_id) {
            $fine = Fine::find($fine_id);
            
            $history = [
                'user_id' => $dto->user_id,
                'author' => Auth::user()->name.' '.Auth::user()->last_name,
                'author_id' => Auth::user()->id,
                'date' => $dto->date,
                'description' => isset($dto->comment) ? 'Удален штраф №' . $fine_id . " " . $fine->name . ', причина: ' . $dto->comment : 'Штраф',
                'created_at' => Carbon::now(Auth::user()->timezone()),
                'updated_at' => Carbon::now(Auth::user()->timezone())
            ];
            TimetrackingHistory::create($history);

          UserFine::turnOffFine($dto->user_id, $fine_id, $dto->date);
        }
    }
}
