<?php

namespace App\Jobs;

use App\Api\BitrixOld\Lead\PaymentLead;
use App\Models\CentralUser;
use App\Models\Tariff\TariffPayment;
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
        private readonly CentralUser   $user,
        private readonly TariffPayment $tariffPayment)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->createPaymentLead($this->user, $this->tariffPayment);
    }

    private function createPaymentLead(
        CentralUser   $user,
        TariffPayment $payment,
    ): void
    {
        $lead = (new PaymentLead(
            $user,
            $payment,
            tenant('id'),
            null
        ))
            ->setNeedCallback(false)
            ->publish();

        $payment->lead_id = $lead['result'];
        $payment->save();
    }
}
