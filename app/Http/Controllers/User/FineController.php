<?php

namespace App\Http\Controllers\User;

use App\DTO\Fine\UpdateUserFinesDTO;
use App\Fine;
use App\Http\Controllers\Controller;
use App\Http\Requests\Fine\UpdateUserFinesRequest;
use App\TimetrackingHistory;
use App\UserDescription;
use App\UserFine;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class FineController extends Controller
{
    /**
     * Update the resources.
     *
     * @param UpdateUserFinesRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(UpdateUserFinesRequest $request): JsonResponse
    {
        $data = $request->toDto();

        $can_delete = Auth::user()->hasPermissionTo('fines_edit');

        $ud = UserDescription::query()->where('user_id', $data->userId)->first();

        if ($ud && $ud->is_trainee == 1) return response()->json([
            "message" => 'Не удалось сохранить изменения. Стажеру нельзя ставить штрафы'
        ]);

        $currentUserFines = UserFine::query()->whereDate('day', $data->date)
            ->where('user_id', $data->userId)
            ->where('status', UserFine::STATUS_ACTIVE)
            ->pluck('fine_id')
            ->toArray();

        $deleteFines = array_diff($currentUserFines, $data->fines);
        $newFines = array_diff($data->fines, $currentUserFines);

        $resultTransaction = false;
        DB::transaction(function () use (&$resultTransaction, $data, $newFines, $deleteFines, $can_delete) {
            $this->addUserFines($data, $newFines, $data->comment);

            if ($can_delete) {
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
     * @param UpdateUserFinesDTO $dto
     * @param array $newFines
     * @param string|null $comment
     */
    protected function addUserFines(UpdateUserFinesDTO $dto, array $newFines, string $comment = null)
    {
        foreach ($newFines as $fineId) {
            $data = [
                'user_id' => $dto->userId,
                'fine_id' => $fineId,
                'day' => $dto->date,
                'note' => null
            ];

            $fine = UserFine::query()->whereDate('day', $dto->date)
                ->where('user_id', $dto->userId)
                ->where('status', UserFine::STATUS_INACTIVE)
                ->where('fine_id', $fineId)
                ->first();

            if (is_null($fine)) {
                $fine = (new UserFine)->addUserFine($data);
            } else {
                $fine = UserFine::turnOnFine($dto->userId, $fineId, $dto->date);
            }

            $history = [
                'user_id' => $dto->userId,
                'author' => Auth::user()->name . ' ' . Auth::user()->last_name,
                'author_id' => Auth::user()->id,
                'date' => $dto->date,
                'description' => isset($comment) ? 'Штраф, причина: ' . $comment : 'Штраф',
                'payload' => json_encode([
                    'type' => 'fine',
                    'fine_id' => $fine->getKey(),
                    'user_id' => $dto->userId,
                    'day' => $dto->date,
                    'note' => $dto->comment,
                ])
            ];

            TimetrackingHistory::query()->create($history);

            UserFine::updateTimetracking($dto->userId, $dto->date);
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
        foreach ($deleteFines as $fineId) {
            $fine = Fine::find($fineId);

            $history = [
                'user_id' => $dto->userId,
                'author' => Auth::user()->name . ' ' . Auth::user()->last_name,
                'author_id' => Auth::user()->id,
                'date' => $dto->date,
                'description' => isset($dto->comment) ? 'Удален штраф №' . $fineId . " " . $fine->name . ', причина: ' . $dto->comment : 'Штраф',
                'created_at' => Carbon::now(Auth::user()->timezone()),
                'updated_at' => Carbon::now(Auth::user()->timezone())
            ];
            TimetrackingHistory::create($history);

            UserFine::turnOffFine($dto->userId, $fineId, $dto->date);
        }
    }
}
