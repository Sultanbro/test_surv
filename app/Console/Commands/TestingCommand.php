<?php

namespace App\Console\Commands;

use App\Service\Sms\ReceiverDto;
use App\Service\Sms\SmsInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Log;
use Throwable;

class TestingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        Log::channel('slackNotification')->info('New user created', [
            'name' => "test",
            'email' => "test@test.com",
        ]);
    }
}
