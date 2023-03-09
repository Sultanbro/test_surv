<?php

namespace App\Console\Commands;

use App\ProfileGroup;
use App\Repositories\Timetrack\TimetrackRepository;
use App\Repositories\TimeTrackingRepository;
use App\Timetracking;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CountHours extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'count:hours {date?} {user_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Selected date
     */
    protected Carbon $date;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
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
        $userId = (int) $this->argument('user_id') ?? null;
        $date   = $this->argument('date') ?? now()->format('Y-m-d');

        $timeTrackRecords = (new TimeTrackingRepository)->getNonUpdatedTimeTrackWithUserByDate($userId, $date)->get();

        foreach($timeTrackRecords as $record)
        {
            $user = $record->user;
            if ($user)
            {
                $userSchedule = $user->schedule();
                $enterTime  = $record->enter;
                $exitTime   = $record->exit;
                $minutes    = $this->calculateMinutes($enterTime, $exitTime);

                $record->update([
                    'total_hours' => $minutes
                ]);
            }
        }
    }

    /**
     * @param Carbon $enterTime
     * @param Carbon $exitTime
     * @return float
     */
    private function calculateMinutes(
        Carbon $enterTime,
        Carbon $exitTime
    ): float
    {
        $maxWorkMinutesPerDay = 480;
        $lunchTime      = 60;
        $diffInMinutes  = $enterTime->diffInMinutes($exitTime) - $lunchTime;

        return $diffInMinutes < 480 ? $diffInMinutes : $maxWorkMinutesPerDay;
    }
}
