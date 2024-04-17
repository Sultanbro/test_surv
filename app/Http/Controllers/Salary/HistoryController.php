<?php

namespace App\Http\Controllers\Salary;

use App\Http\Controllers\Controller;
use App\Http\Requests\History\ReasonRequest;
use App\Salary;
use App\TimetrackingHistory;
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

        $amount = $payload['amount'];
        $salary = Salary::query()
            ->findOrFail($payload['salary_id']);
        $salary->update([
            $type => $salary->{$type} + $amount
        ]);

        $payload['restored_by'] = $this->template($request);
        $history->payload = json_encode($payload);
        $history->save();
        return response()->json($history);
    }

    private function template(ReasonRequest $request): string
    {
        return "<br>" . $request->action() . " сотрудником " . $request->user()->name . ' ' . $request->user()->last_name . ' ' . date('d.m.Y')
            . "<br>Причина: " . $request->reason();
    }

    public function delete(ReasonRequest $request, TimetrackingHistory $history): JsonResponse
    {
        $payload = json_decode($history->payload, true);

        $type = $payload['type'];

        $amount = $payload['amount'];
        $salary = Salary::query()
            ->findOrFail($payload['salary_id']);
        $salary->update([
            $type => $salary->{$type} - $amount
        ]);

        $payload['deleted_by'] = $this->template($request);
        $history->payload = json_encode($payload);
        $history->save();
        $history->delete();
        return response()->json($history);
    }
}