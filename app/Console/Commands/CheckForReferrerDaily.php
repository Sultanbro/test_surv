<?php

namespace App\Console\Commands;

use App\Service\Referral\ReferrerSalaryService;
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
     * @param ReferrerSalaryService $service
     * @return int
     */
    public function handle(ReferrerSalaryService $service): int
    {
        $service->updateSalaries($this->argument('user'));
        return 1;
    }
}
