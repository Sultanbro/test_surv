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
        $date = now()->format('Y-m-d');
        $givenDate = $this->argument('date') ? Carbon::parse($this->argument('date')) : null;

        $timeTrackRecords = (new TimeTrackingRepository)
            ->getNonUpdatedTimeTrackWithUser($userId)
            ->when($givenDate,
                function (Builder $query) use ($givenDate) {
                    $query->whereYear('enter', '=', $givenDate->year);
                    $query->whereMonth('enter', '=', $givenDate->month);
                },
                function (Builder $query) use ($date) {
                    $query->whereDate('enter', $date);
                }
            )
            ->get();

        foreach ($timeTrackRecords as $record) {
            /** @var User $user */
            $user = $record->user;
            if ($user) {
                $userSchedule = $user->schedule();
                $enterTime = $record->enter;
                $exitTime = $record->exit;
                $minutes = $this->calculateMinutes($userSchedule, $enterTime, $exitTime);
                dump('user ID:' . $user->id);
                dump('рабочые минуты:' . $minutes);
                dump('дата:' . Carbon::parse($record->enter)->toDateTimeString());

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
        dd($schedule);
        $maxWorkMinutesPerDay = max($schedule['start']->addMinutes(30)->diffInMinutes($schedule['end']) - $lunchTime, 0);
        $diffInMinutes = $enterTime->diffInMinutes($exitTime) - $lunchTime;

        return min($diffInMinutes, $maxWorkMinutesPerDay);
    }
}

