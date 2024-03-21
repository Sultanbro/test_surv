<?php

namespace App\Console\Commands;

use App\Timetracking;
use App\Timetracking as Model;
use Carbon\Carbon;
use Illuminate\Console\Command;

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
            $currentDate = $from;
            $dayBeforeCurrentDate = $from->copy()->subDay();
            $records = Timetracking::with('user')
                ->whereHas('user')
                ->whereBetween('enter', [$dayBeforeCurrentDate, $currentDate])
//            ->where('status', Model::DAY_STARTED)
                ->get();

            /** @var Timetracking $record */
            foreach ($records as $record) {
                dump($record->user_id . ' date: ' . $currentDate->toDateTimeString());
                /** @var Carbon $workEndTime */
                $workEndTime = $record->user->schedule()['end'];


                if ($record->isWorkEndTimeSetToNextDay($workEndTime)) {
                    $workEndTime->addDays();
                }

                if (!$workEndTime->isBefore($currentDate->addDay())) {
                    continue;
                }


                $record->setExit($workEndTime)
                    ->setStatus(Timetracking::DAY_ENDED)
                    ->addTime($workEndTime, $record->user->timezone())
                    ->save();

                $this->line("Для сотрудника с ID " . $record->user_id . " рабочий день завершен автоматический в " . $workEndTime->format('H:i'));
            }

            $from->addDay();
        }
    }
}