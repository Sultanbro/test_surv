<?php

namespace App\Console\Commands;

use App\Repositories\TimeTrackingRepository;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class CountHours extends Command
{

    protected $signature = 'count:hours {date?} {user_id?}';

    protected $description = 'Command description';

    protected Carbon $date;

    public function handle(): void
    {
        $userId = (int)$this->argument('user_id') ?? null;
        $currentDate = now()->format('Y-m-d');
        $givenDate = Carbon::parse($this->argument('date') ?? $currentDate);

        $timeTrackRecords = (new TimeTrackingRepository)
            ->getNonUpdatedTimeTrackWithUser($userId)
            ->whereDate('enter', $givenDate)
             ->get();

        foreach ($timeTrackRecords as $record) {
            /** @var User $user */
            $user = $record->user;
            if ($user) {
                $userSchedule = $user->schedule();
                $enterTime = $record->enter;
                $exitTime = $record->exit;
                $minutes = $this->calculateMinutes($userSchedule, $enterTime, $exitTime);
                dump('user ID: ' . $user->id);
                dump('рабочые минуты: ' . $minutes);
                dump('дата: ' . Carbon::parse($record->enter)->toDateTimeString());

                $record->update([
                    'total_hours' => $minutes,
                    'updated' => 1,
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
        $lunchTime = $this->getLaunchTime($schedule);
        $maxWorkMinutesPerDay = max($schedule['start']->addMinutes(30)->diffInMinutes($schedule['end']) - $lunchTime, 0);
        $diffInMinutes = $enterTime->diffInMinutes($exitTime) - $lunchTime;

        return min($diffInMinutes, $maxWorkMinutesPerDay);
    }

    private function getLaunchTime(array $schedule)
    {
        return $schedule['rest_time'] ?: 60;
    }
}

