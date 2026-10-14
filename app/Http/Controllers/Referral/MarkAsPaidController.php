<?php

namespace App\Http\Controllers\Referral;

use App\Http\Controllers\Controller;
use App\Http\Requests\Referral\PaidRequest;
use App\Models\Referral\ReferralSalary;
use App\User;
use Illuminate\Http\JsonResponse;

class MarkAsPaidController extends Controller
{
    public function pay(PaidRequest $request, int $user): JsonResponse
    {
        $data = $request->validated();
        $salary = ReferralSalary::query()->find($data['id']);

        if (!$salary) {
            return response()->json(['message' => 'transaction not found']);
        }

        $salary->update(
            [
                'is_paid' => $data['paid'],
                'comment' => $data['comment'] ?? null
            ]
        );

        return response()->json(['message' => 'success']);
    }
}
