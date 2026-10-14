<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Service\Invoice\UpdateDealInBitrix;
use App\Service\Sms\ReceiverDto;
use App\Service\Sms\SmsInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Log;
use Throwable;

class TestingCommand extends Command
{
    protected $signature = 'ping:pong';

    protected $description = 'just for fast testing... :)';

    public function handle(): void
    {
        $invoice = Invoice::query()->where('lead_id', 179216)->first();
        $updateService = new UpdateDealInBitrix($invoice);
        $updateService->send();
    }
}
