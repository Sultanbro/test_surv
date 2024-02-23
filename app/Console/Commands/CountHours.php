<?php

namespace App\Console\Commands;

use App\Repositories\TimeTrackingRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;

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
        $userId = (int)$this->argument('user_id') ?? null;
        $date = $this->argument('date') ?? now()->format('Y-m-d');

        $timeTrackRecords = (new TimeTrackingRepository)->getNonUpdatedTimeTrackWithUserByDate($userId, $date)->get();

        foreach ($timeTrackRecords as $record) {
            $user = $record->user;
            if ($user) {
                $userSchedule = $user->schedule();
                $enterTime = $record->enter;
                $exitTime = $record->exit;
                $minutes = $this->calculateMinutes($userSchedule, $enterTime, $exitTime);

                $record->update([
                    'total_hours' => $minutes
                ]);
            }
        }
    }

    /**
     * @param array $schedule
     * @param Carbon $enterTime
     * @param Carbon $exitTime
     * @return float
     */
    private function calculateMinutes(
        array  $schedule,
        Carbon $enterTime,
        Carbon $exitTime
    ): float
    {
        $lunchTime = $schedule['rest_time'] ?? 60;

        $maxWorkMinutesPerDay = max($schedule['start']->addMinutes(30)->diffInMinutes($schedule['end']) - $lunchTime, 0);
        $diffInMinutes = $enterTime->diffInMinutes($exitTime) - $lunchTime;
        dump($maxWorkMinutesPerDay . " " . $diffInMinutes . " " . $lunchTime);
        return min($diffInMinutes, $maxWorkMinutesPerDay);
    }
}

