<?php

namespace App\Jobs\Referral;

use App\Service\Referral\Core\ReferrerInterface;
use App\Service\Referral\Core\StatusServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessTouchReferrerStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public readonly ReferrerInterface $referrer
    )
    {
    }

    public function handle(StatusServiceInterface $service): void
    {
        $service->touch($this->referrer);
    }
}
