<?php

namespace App\Console\Commands;

use App\Timetracking;
use App\Timetracking as Model;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SetExitTimetracking extends Command
{
    protected $signature = 'timetracking:check {date?}';

    protected $description = 'Автоматическое завершение рабочего дня';

    public function handle(): void
    {
        $currentDate = Carbon::parse($this->argument('date') ?? now()->toDateString());
        $dayBeforeCurrentDate = Carbon::parse($this->argument('date') ?? now()->toDateString())->subDay();
        $records = Model::query()
            ->withWhereHas('user')
            ->whereBetween('enter', [$dayBeforeCurrentDate, $currentDate])
            ->where('status', Model::DAY_STARTED)
//            ->whereNull('exit') TODO:check in feature
            ->get();

        /** @var Timetracking $record */
        foreach ($records as $record) {
            User::setExit($record, $currentDate);
        }
    }
}