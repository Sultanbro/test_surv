<?php

namespace App\Http\Controllers\Referral;

use App\Http\Controllers\Controller;
use App\Http\Requests\Referral\PaidRequest;
use App\User;
use Illuminate\Http\JsonResponse;

class MarkAsPaidController extends Controller
{
    public function __invoke(PaidRequest $request, User $user): JsonResponse
    {
        $data = $request->validated();
        $referrer = $user->referrer;
        $salary = $referrer->salaries()
            ->find($data['id']);
        if (!$salary) {
            return response()->json(['message' => 'transaction not found']);
        }
        $salary->update(
            [
                'is_paid' => $data['paid'],
                'note' => $data['comment'] ?? null
            ]
        );

        return response()->json(['message' => 'success']);
    }
}
