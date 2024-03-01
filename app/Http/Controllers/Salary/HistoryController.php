<?php

namespace App\Http\Controllers\Salary;

use App\Http\Controllers\Controller;
use App\Http\Requests\History\ReasonRequest;
use App\Salary;
use App\TimetrackingHistory;
use App\User;
use App\UserFine;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class HistoryController extends Controller
{
    public function restore(ReasonRequest $request, int $history): JsonResponse
    {
        /** @var TimetrackingHistory $history */
        $history = TimetrackingHistory::withTrashed()->findOrFail($history);
        $history->restore();
        $payload = json_decode($history->payload, true);
        $type = $payload['type'];

        if ($type == 'fine') {
            (new UserFine)->addUserFine($payload['record']);
        } else {
            $amount = $payload['amount'];
            $salary = Salary::query()
                ->findOrFail($payload['salary_id']);
            $salary->update([
                $type => $salary->{$type} + $amount
            ]);
        }

        $payload['restored_by'] = $this->template($request);
        $history->payload = json_encode($payload);
        $history->save();
        return response()->json($history);
    }

    private function template(ReasonRequest $request): string
    {
        return "<br>" . $request->action() . " сотрудником" . $request->user()->name . ' ' . date('d.m.Y')
            . "<br>Причина: " . $request->reason();
    }

    public function delete(ReasonRequest $request, TimetrackingHistory $history): JsonResponse
    {
        $payload = json_decode($history->payload, true);
        $type = $payload['type'];
        if ($type == 'fine') {
            /** @var User $user */
            $user = User::query()->find($payload['user_id']);

            $userFine = $user->fines()->where('day', Carbon::parse($payload['day'])->format("Y-m-d"))
                ->find($payload['fine_id']);

            if ($userFine) {
                $payload['record'] = $userFine->toArray();
                $user->fines()->detach($userFine);
            }

        } else {
            $amount = $payload['amount'];
            $salary = Salary::query()
                ->findOrFail($payload['salary_id']);
            $salary->update([
                $type => $salary->{$type} - $amount
            ]);
        }

        $payload['deleted_by'] = $this->template($request);
        $history->payload = json_encode($payload);
        $history->save();
        $history->delete();
        return response()->json($history);
    }
}