<?php

namespace App\Console\Commands\ManualRun;

use App\Kpi;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class FixKpiActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:activities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $kpi = Kpi::query()->get();
        return CommandAlias::SUCCESS;
    }
}
