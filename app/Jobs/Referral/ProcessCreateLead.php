<?php

namespace App\Jobs\Referral;

use App\Service\Referral\Core\LeadServiceInterface;
use App\Service\Referral\Core\RequestDto;
use App\Service\Referral\Core\ReferrerInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCreateLead implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private readonly ReferrerInterface $referrer
        , private readonly RequestDto      $request
    )
    {
    }

    /**
     * Execute the job.
     *
     * @param LeadServiceInterface $leadService
     * @return void
     */
    public function handle(LeadServiceInterface $leadService): void
    {
        $leadService->create($this->referrer, $this->request);
    }
}
