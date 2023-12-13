<?php

namespace App\Console\Commands\Analytics;

use App\Service\Analytics\CreatePivotAnalyticsInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreatePivotAnalytics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:pivots';

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
        $this->analytics->create();
        $this->cleanDuplicates();
        $this->line('end');
    }

    private function cleanDuplicates(): void
    {
        $currentDate = Carbon::now()->startOfMonth()->format("Y-m-d");

        $idsToKeep = DB::table('analytic_rows')
            ->selectRaw('MIN(id) as min_id')
            ->where('name', '!=', 'name')
            ->whereDate('date', $currentDate)
            ->groupBy('name')
            ->pluck('min_id');

        DB::table('analytic_rows')
            ->whereNotIn('id', $idsToKeep)
            ->where('name', '!=', 'name')
            ->whereDate('date', $currentDate)
            ->delete();
    }
}
