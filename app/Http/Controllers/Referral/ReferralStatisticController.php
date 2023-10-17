<?php

namespace App\Http\Controllers\Referral;

use App\Facade\Referring;
use App\Http\Controllers\Controller;
use App\Http\Requests\Referral\Request;
use App\Http\Resources\Users\ReferralUrlResource;
use App\Jobs\Referral\ProcessCreateBitrixLead;
use App\Models\Referral\Referral;
use App\User;
use Illuminate\Http\JsonResponse;
use Throwable;

class ReferralStatisticController extends Controller
{
    public function generate(): ReferralUrlResource
    {
        /** @var User $user */
        $user = auth()->user();
        return ReferralUrlResource::make(Referring::generateReferral($user));
    }

    /**
     * @throws Throwable
     */
    public function request(Request $request, Referral $referral): JsonResponse
    {
        $referrer = Referring::request($referral);

        // here we calculate the referrer salary and transfer to him in transaction
        Referring::apply($referrer);

        ProcessCreateBitrixLead::dispatch($referral, $request->validated())
            ->onQueue('bitrix-lead')
            ->afterCommit();

        return response()->json([
            'message' => 'successfully processed!'
        ]);
    }
}
