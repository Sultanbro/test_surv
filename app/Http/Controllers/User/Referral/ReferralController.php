<?php

namespace App\Http\Controllers\User\Referral;

use App\Facade\Referring;
use App\Http\Controllers\Controller;
use App\Http\Resources\Users\ReferralUrlResource;
use App\Models\User\Referral\Referral;
use App\User;
use Illuminate\Http\Response;

class ReferralController extends Controller
{
    public function generate(): ReferralUrlResource
    {
        /** @var User $user */
        $user = auth()->user();
        return ReferralUrlResource::make(Referring::generateReferral($user));
    }

    public function determinate(Referral $referral): Response
    {
        Referring::determinateReferral($referral)->;
        return response()->noContent();
    }
}
