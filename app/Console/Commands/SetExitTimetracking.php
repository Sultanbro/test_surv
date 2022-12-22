<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Timetracking as Model;

class SetExitTimetracking extends Command
{
    protected $signature = 'timetracking:check';

    protected $description = 'Автоматическое завершение рабочего дня';

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
        $records = Model::with('user')
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
                ->setStatus(Model::DAY_ENDED)
                ->addTime($workEndTime, $record->user->timezone())
                ->save();

            $this->line("Для сотрудника с ID ".$record->user_id." рабочий день завершен автоматический в ".$workEndTime->format('H:i'));
        }
    }
}