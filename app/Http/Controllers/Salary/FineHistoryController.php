<?php

namespace App\Http\Controllers\Salary;

use App\Fine;
use App\Http\Controllers\Controller;
use App\Http\Requests\History\ReasonRequest;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class FineHistoryController extends Controller
{
    public function restore(ReasonRequest $request, User $user, Fine $fine): JsonResponse
    {
        $user->fines()->updateExistingPivot($fine, [
            'note' => $this->template($request),
            'deleted_at' => null
        ]);
        return response()->json();
    }

    private function template(ReasonRequest $request): string
    {
        return "<br>" . $request->action() . " сотрудником" . $request->user()->name . ' ' . date('d.m.Y')
            . "<br>Причина: " . $request->reason();
    }

    public function delete(ReasonRequest $request, User $user, Fine $fine): Response
    {
        $user->fines()->updateExistingPivot($fine, [
            'note' => $this->template($request),
            'deleted_at' => now()->toDateString()
        ]);
        return response()->noContent();
    }
}