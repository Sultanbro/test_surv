<?php

namespace App\Console\Commands\Analytics;

use App\Service\Analytics\CreatePivotAnalyticsInterface;
use Illuminate\Console\Command;

class CreatePivotAnalytics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:pivots {group?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create AnalyticStats for group';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        private readonly CreatePivotAnalyticsInterface $analytics
    )
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->line('start creating pivot tables:');
        $this->analytics->create($this->argument('group'));
        $this->line('end');
    }
}
