<?php

namespace App\Console\Commands;

use App\Timetracking;
use App\Timetracking as Model;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class SetExitTimetrackingForMonth extends Command
{
    protected $signature = 'timetracking:check-month {date?}';

    protected $description = 'Автоматическое завершение рабочего дня для месяца';

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
        $from = Carbon::parse($this->argument('date') ?? now())->startOfMonth();
        $to = Carbon::parse($this->argument('date') ?? now())->endOfMonth();

        while ($from->lessThan($to)) {
            dump($from->toDateTimeString());
            $currentDate = $from->copy();
            $dayBeforeCurrentDate = $from->copy()->subDay();
            $records = Model::query()
                ->withWhereHas('user')
                ->whereBetween('enter', [$dayBeforeCurrentDate, $currentDate])
                ->where('status', Model::DAY_STARTED)
                ->get();

            $this->touch($records, $currentDate);
            $from->addDay();
        }
    }

    private function touch(Collection $records, Carbon $currentDate): void
    {
        /** @var Timetracking $record */
        foreach ($records as $record) {
            /** @var Carbon $workEndTime */
            $workEndTime = $record->user->schedule()['end'];


            if ($record->isWorkEndTimeSetToNextDay($workEndTime)) $workEndTime->addDays();

            if (!$workEndTime->isBefore($currentDate->addDay())) continue;

            $record->setExit($workEndTime)
                ->setStatus(Timetracking::DAY_ENDED)
                ->addTime($workEndTime, $record->user->timezone())
                ->save();
        }
    }
}