<?php

namespace App\Http\Controllers\Referral;

use App\Enums\SalaryResourceType;
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
        $referrer->salaries()
            ->where('award', $data['amount'])
            ->where('resource', SalaryResourceType::REFERRAL)
            ->where('comment_award', $user->id)
            ->update(
                [
                    'is_paid' => 1,
                    'note' => $data['comment'] ?? null
                ]
            );

        return response()->json([
            'message' => 'success'
        ]);
    }
}
