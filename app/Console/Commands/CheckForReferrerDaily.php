<?php

namespace App\Console\Commands;

use App\Service\Referral\ForReferrerDaily;
use Illuminate\Console\Command;

class CheckForReferrerDaily extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'referrer:daily {user?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @param ForReferrerDaily $service
     * @return int
     */
    public function handle(ForReferrerDaily $service): int
    {
        $service->handle($this->argument('user'));
        return 1;
    }
}
