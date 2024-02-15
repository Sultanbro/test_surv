<?php

namespace App\Console\Commands;

use App\Service\Sms\ReceiverDto;
use App\Service\Sms\SmsInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
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
    public function handle(SmsInterface $sms): void
    {
        $receiver = new ReceiverDto(
            '77073572802',
            'Вайчеслав'
        );

        $data = $sms->send($receiver, 'код подтверждение: 12555');
        $this->newLine();
        $this->alert(Str::replace([',', '{', '}'], PHP_EOL, json_encode($data)));
    }
}
