<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Timetracking;

class Timetracking extends Command
{
    protected $signature = 'timetracking:check';

    protected $description = 'Запуск проверки таблиц учетам времени';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $records = Timetracking::with('user')
            ->where(function($query) {
                $query->whereDate('enter', now())
                    ->orWhereDate('enter', now()->subDay());
            })
            ->workdayStarted()
            ->get();

        foreach ($records as $record) {

            if(!$record->user) {
                continue;
			}

            $workEndTime = $record->user->schedule()['end'];

            if($record->isWorkEndTimeSetToNextDay($workEndTime)) {
                $workEndTime->addDays(1);
            }

            if (!$workEndTime->isPast()) {
                continue;
            }

            $record->setExit($workEndTime)
                ->setStatus(Timetracking::DAY_ENDED)
                ->addTime($workEndTime)
                ->save();

            $this->line("Для сотрудника с ID ".$record->user_id." рабочий день завершен автоматический в ".$workEndTime->format('H:i'));
        }
    }
}