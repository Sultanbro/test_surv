<?php

namespace App\Http\Controllers\Referral;

use App\Facade\Referring;
use App\Http\Controllers\Controller;
use App\Http\Requests\Referral\Request;
use App\Http\Resources\Referral\ReferralUrlResource;
use App\Service\Referral\Core\LeadServiceInterface;
use App\User;
use Illuminate\Http\JsonResponse;

class ReferralController extends Controller
{
    public function __construct(
        private LeadServiceInterface $leadService
    )
    {
    }

    public function url(): ReferralUrlResource
    {
        /** @var User $user */
        $user = auth()->user();
        return ReferralUrlResource::make(Referring::url($user));
    }

    public function request(Request $request, User $user): JsonResponse
    {

        $this->leadService->create($user, $request->toDto());
//        ProcessCreateLead::dispatch($user, $request->toDto());
        return response()->json([
            'message' => 'successfully processed!'
        ]);
    }
}
