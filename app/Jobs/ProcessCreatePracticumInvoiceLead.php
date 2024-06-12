<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Service\Invoice\StoreDealInBitrix;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCreatePracticumInvoiceLead implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private readonly Invoice $invoice
    )
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
        $service = new StoreDealInBitrix($this->invoice);
        $service->send();
    }

    public function fail(Exception $exception): void
    {
        slack(__CLASS__ . ": cant send the lead to bitrix, reason:  " . $exception->getMessage());
    }
}
