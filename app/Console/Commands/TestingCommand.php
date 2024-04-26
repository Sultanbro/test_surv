<?php

namespace App\Console\Commands;

use App\Service\Sms\ReceiverDto;
use App\Service\Sms\SmsInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Log;
use Throwable;

class TestingCommand extends Command
{
    protected $signature = 'test:service';

    protected $description = 'just for fast testing... :)';

    public function handle(): void
    {
        slack('the scheduler is working . ' . Carbon::now()->toDateTimeString());
    }
}
