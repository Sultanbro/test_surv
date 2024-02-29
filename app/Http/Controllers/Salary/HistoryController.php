<?php

namespace App\Http\Controllers\Salary;

use App\Http\Controllers\Controller;
use App\Salary;
use App\TimetrackingHistory;
use App\UserFine;
use Illuminate\Http\Response;

class HistoryController extends Controller
{
    public function restore(int $history): Response
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
                $type => $type + $amount
            ]);
        }
        return response()->noContent();
    }

    public function delete(TimetrackingHistory $history): Response
    {
        $payload = json_decode($history->payload, true);
        $type = $payload['type'];
        if ($type == 'fine') {
            $userFine = UserFine::query()->find($payload['fine_id']);
            $payload['record'] = $userFine->toArray();
            $history->payload = json_encode($payload);
            $history->save();
            $userFine->delete();

        } else {
            $amount = $payload['amount'];
            $salary = Salary::query()
                ->findOrFail($payload['salary_id']);
            $salary->update([
                $type => $salary->{$type} - $amount
            ]);
        }

        $history->delete();
        return response()->noContent();
    }
}