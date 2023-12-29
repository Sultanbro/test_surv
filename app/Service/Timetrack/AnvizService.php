<?php

namespace App\Service\Timetrack;

use App\Models\Anviz\Time as AnvizTime;
use App\Timetracking;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class AnvizService
{
    public string $date;

    public function __construct()
    {
    }

    public function fetchMarkTimes($date = null): void
    {
        $dateToFetch = Carbon::parse($date ?? now()->toDateString());
        $dates = $this->shouldFetchPreviousDays()
            ? [$dateToFetch, $dateToFetch->subDay()->toDateString(), $dateToFetch->subDays(2)->toDateString()]
            : [$dateToFetch->toDateString()];

        foreach ($dates as $my_date) {
            $this->date = $my_date;
            $this->resolveOneDay();
        }
    }

    private function shouldFetchPreviousDays(): bool
    {
        return now()->dayOfWeek == 1 && now()->format('H:i') == '07:00';
    }

    private function resolveOneDay(): void
    {
        $anvizRecords = AnvizTime::query()
            ->latest('CheckTime')
            ->whereDate('CheckTime', $this->date)
            ->get();
        $usersIds = $this->getUserIds($anvizRecords);
        $timetrackingRecords = $this->getTimetrackingRecords($usersIds);

        foreach ($usersIds as $user_id) {
            $lastAnvizDate = optional($anvizRecords->where('Userid', $user_id)->first())->CheckTime;
            $userRecords = $timetrackingRecords->where('user_id', $user_id);

            $this->handleUserRecords($userRecords, $user_id, $lastAnvizDate);
        }
    }

    private function handleUserRecords($userRecords, $user_id, $lastAnvizDate): void
    {
        // If user doesn't have a Timetracking record for date
        if ($userRecords->isEmpty()) {
            $this->createTimetrackingRecord($user_id, $lastAnvizDate);
        }
    }

    private function createTimetrackingRecord($user_id, $lastAnvizDate): void
    {
        Timetracking::query()
            ->create([
                'enter' => now()->parse($lastAnvizDate)->subHours(6),
                'user_id' => $user_id
            ]);
    }

    // Other methods...

    private function getUserIds($records = null): array
    {
        return $records->unique('Userid')
            ->pluck('Userid')
            ->toArray();
    }

    private function getTimetrackingRecords(array $users): Collection
    {
        return Timetracking::query()
            ->whereDate('enter', $this->date)
            ->whereIn('user_id', $users)
            ->get();
    }
}
