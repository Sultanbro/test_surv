<?php

namespace App\Http\Controllers\User\Referral;

use App\Http\Controllers\Controller;
use App\Http\Resources\Users\ReferralUrlResource;
use App\Models\User\Referral\Referral;
use App\Service\Referral\ReferralDeterminationInterface;
use App\Service\Referral\ReferralGeneratorInterface;
use App\User;
use Illuminate\Http\Response;

class ReferralController extends Controller
{
    public function __construct(
        private readonly ReferralGeneratorInterface     $generator,
        private readonly ReferralDeterminationInterface $determination
    )
    {
    }

    public function generate(): ReferralUrlResource
    {
        /** @var User $user */
        $user = auth()->user();
        return ReferralUrlResource::make($this->generator->generate($user));
    }

    public function determinate(Referral $referral): Response
    {
        $this->determination->determinate($referral);
        return response()->noContent();
    }
}
