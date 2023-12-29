<?php

namespace App\Console\Commands;

use App\Timetracking;
use App\Timetracking as Model;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SetExitTimetracking extends Command
{
    protected $signature = 'timetracking:check {date?}';

    protected $description = 'Автоматическое завершение рабочего дня';

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
        $currentDate = Carbon::parse($this->argument('date') ?? now()->toDateString());
        $dayBeforeCurrentDate = Carbon::parse($this->argument('date') ?? now()->toDateString())->subDay();
        $records = Model::with('user')
            ->where('enter', [$currentDate, $dayBeforeCurrentDate])
//            ->where('status', Model::DAY_STARTED)
            ->get();
        /** @var Timetracking $record */
        foreach ($records as $record) {

            if (!$record->user) {
                continue;
            }

            /** @var Carbon $workEndTime */
            $workEndTime = $record->user->schedule()['end'];

            if ($record->isWorkEndTimeSetToNextDay($workEndTime)) {
                $workEndTime->addDays();
            }

            if (!$workEndTime->isBefore($currentDate->addDay())) {
                continue;
            }

            $record->setExit($workEndTime)
                ->setStatus(Model::DAY_ENDED)
                ->addTime($workEndTime, $record->user->timezone())
                ->save();

            $this->line("Для сотрудника с ID " . $record->user_id . " рабочий день завершен автоматический в " . $workEndTime->format('H:i'));
        }
    }
}