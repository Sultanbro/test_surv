<?php

namespace App\Console\Commands;

use App\Models\Analytics\AnalyticColumn;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class ForTestingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:test';

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
        DB::table('analytic_columns as a')
            ->join(DB::raw('(
        SELECT a.id
        FROM analytic_columns a
        JOIN (
            SELECT name, date, group_id
            FROM analytic_columns
            WHERE name = 31
            GROUP BY name, date, group_id
            HAVING COUNT(*) > 1
        ) b
        ON a.name = b.name AND a.date = b.date AND a.group_id = b.group_id
    ) as c'), 'a.id', '=', 'c.id')
            ->delete();
    }
}
