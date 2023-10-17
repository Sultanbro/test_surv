<?php

namespace App\Jobs\Referral;

use App\Service\Referral\WebhookHandlerServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessHandleWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private readonly array $data
    )
    {
        //
    }

    /**
     * Execute the job.
     *
     * @param WebhookHandlerServiceInterface $service
     * @return void
     */
    public function handle(WebhookHandlerServiceInterface $service): void
    {
        $service->handle($this->data);
    }
}
