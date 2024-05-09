<?php

namespace App\Jobs;

use App\Api\BitrixOld\Lead\PaymentLead;
use App\Models\CentralUser;
use App\Models\Tariff\TariffSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCreatePaymentInvoiceLead implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private readonly CentralUser        $user,
        private readonly TariffSubscription $subscription)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->createPaymentLead($this->user, $this->subscription);
    }

    private function createPaymentLead(
        CentralUser        $user,
        TariffSubscription $subscription,
    ): void
    {
        $lead = (new PaymentLead(
            $user,
            $subscription,
            tenant('id'),
            null
        ))
            ->setNeedCallback(false)
            ->publish();

        $subscription->lead_id = $lead['result'];
        $subscription->save();
    }
}
