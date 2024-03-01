<?php

namespace App\Http\Controllers\Salary;

use App\Http\Controllers\Controller;
use App\Http\Requests\History\ReasonRequest;
use App\Salary;
use App\TimetrackingHistory;
use App\UserFine;
use Illuminate\Http\Response;

class HistoryController extends Controller
{
    public function restore(ReasonRequest $request, int $history): Response
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
        return response()->noContent();
    }

    private function template(ReasonRequest $request): string
    {
        return "<br>" . $request->action() . " сотрудником" . $request->user()->name . ' ' . date('d.m.Y')
            . "<br>Причина: " . $request->reason();
    }

    public function delete(ReasonRequest $request, TimetrackingHistory $history): Response
    {
        $payload = json_decode($history->payload, true);
        $type = $payload['type'];
        if ($type == 'fine') {
            $userFine = UserFine::query()->find($payload['fine_id']);
            $payload['record'] = $userFine->toArray();
            $userFine->delete();
        } else {
            $amount = $payload['amount'];
            $salary = Salary::query()
                ->findOrFail($payload['salary_id']);
            $salary->update([
                $type => $salary->{$type} - $amount
            ]);
        }

        $payload['deleted_by'] = $this->template($request->user());
        $history->payload = json_encode($payload);
        $history->save();
        $history->delete();
        return response()->noContent();
    }
}