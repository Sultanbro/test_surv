<?php

namespace App\Console\Commands;

use App\Timetracking;
use App\Timetracking as Model;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class SetExitTimetracking extends Command
{
    protected $signature = 'timetracking:check {date?} {user_id?}';

    protected $description = 'Автоматическое завершение рабочего дня';

    public function handle(): void
    {
        $currentDate = Carbon::parse($this->argument('date') ?? now()->toDateString());
        dd($currentDate);
        $dayBeforeCurrentDate = Carbon::parse($this->argument('date') ?? now()->toDateString())->subDay();
        $records = Model::query()
            ->when($this->argument('user_id'), fn(Builder $query) => $query->where('user_id', $this->argument('user_id')))
            ->whereDate('enter', '>=', $dayBeforeCurrentDate)
            ->whereDate('enter', '<=', $currentDate)
            ->where('status', Model::DAY_STARTED)
//            ->whereNull('exit') TODO:check in feature
            ->get();

        /** @var Timetracking $record */
        foreach ($records as $record) {
            User::setExit($record, $currentDate);
        }
    }
}