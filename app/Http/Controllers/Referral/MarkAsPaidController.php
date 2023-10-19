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
        $query = $referrer->salaries()
            ->where('award', $data['amount'])
            ->where('resource', SalaryResourceType::REFERRAL)
            ->where('comment_award', $user->id);
        if ($data['type'] = 3) {
            $user_appliet_at = $user->description()?->first()?->applied;
            $query->whereDate('date', $user_appliet_at);
        }
        $query->update(
            [
                'is_paid' => 1,
                'note' => $data['comment'] ?? null
            ]
        );

        return response()->json(['message' => 'success']);
    }
}
