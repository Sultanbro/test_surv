<?php

namespace App\Jobs\Referral;

use App\Service\Referral\Core\ReferralInterface;
use App\Service\Referral\Core\ReferralLeadServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCreateBitrixLead implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private readonly ReferralInterface $referral
        , private readonly array           $request
    )
    {
    }

    /**
     * Execute the job.
     *
     * @param ReferralLeadServiceInterface $leadService
     * @return void
     */
    public function handle(ReferralLeadServiceInterface $leadService): void
    {
        $leadService->create($this->referral, $this->request);
    }
}
