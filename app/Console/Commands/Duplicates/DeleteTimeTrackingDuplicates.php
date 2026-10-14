<?php

namespace App\Console\Commands\Duplicates;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteTimeTrackingDuplicates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:track-duplicates {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The command to delete time tracking duplicates';


    public function handle(): void
    {
        $dateStart = Carbon::parse($this->argument('date') ?? now())->startOfMonth();
        $dateEnd = Carbon::parse($this->argument('date') ?? now())->endOfMonth();

        DB::table(DB::raw('timetracking as t'))
            ->join(DB::raw('(SELECT user_id, DATE(enter) AS enter_date
                    FROM timetracking
                    WHERE DATE(enter) BETWEEN ? AND ?
                    GROUP BY user_id, DATE(enter)
                    HAVING COUNT(*) > 1) AS duplicates'), function ($join) {
                $join->on('t.user_id', '=', 'duplicates.user_id')
                    ->whereRaw('DATE(t.enter) = duplicates.enter_date');
            })
            ->where('t.updated', '<>', 2)
            ->setBindings([$dateStart, $dateEnd]) // Binding parameters
            ->delete();
    }
}
