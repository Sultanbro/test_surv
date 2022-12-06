<?php

namespace App\Console\Commands;

use App\ProfileGroup;
use App\Timetracking;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CountHours extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'count:hours {date?} {user_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Selected date
     */
    protected Carbon $date;

    /**
     * Create a new command instance.
     *
     * @return void
     */
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
        $this->setDate();
        
        $timetrackingRecords = Timetracking::query()
                ->select('id', 'enter', 'exit', 'total_hours', 'user_id')
                ->whereDate('enter',  $this->date->format('Y-m-d'))
                ->whereNotNull('exit')
                ->where('updated', 0) // non updated records
                ->get();

        $users = User::whereIn('id', $timetrackingRecords->pluck('user_id')->toArray())->get();

        foreach($timetrackingRecords as $record) {

            $user = $users->where('id', $record->user_id)->first();

            if( !$user ) {
                continue;
            }

            $schedule = $user->schedule();

            $workStart = $schedule['start'];
            $workEnd   = $schedule['end'];
            
            $timeStart = $this->countFromShiftStartTime($workStart, $record->enter);
            $timeEnd   = Carbon::parse($record->exit); // не учитываем конец дня, засчитываем как переработку

            $minutes = $timeEnd->diffInMinutes($timeStart);
            $minutes = $this->subtractLunchTime($minutes);

            if ($minutes <= 0) {
                $minutes = 0;
            }

            $record->update(['total_hours' => $minutes]);
        }

        $this->line('Было найдено нередактированных записей: '. count($timetrackingRecords));

    }

    /**
     * Время начала работы меньше чем время начала смены, значит считаем от начала смены
     * @param Carbon $workStartAt
     * @param Carbon $enterAt
     * @return Carbon
     */
    protected function countFromShiftStartTime(Carbon $workStartAt, Carbon $enterAt)
    {
        $workStartAt = $this->date->setTimeFrom($workStartAt);

        if ( $workStartAt->diffInMinutes($enterAt, false) > 0 ) {
            return $workStartAt;
        }
        
        return $enterAt;
    }

    /**
     * Вычесть обед из отработанных минут
     * @param int|float $minutes
     * @return int|float
     */
    protected function subtractLunchTime(int|float $minutes)
    {
        $lunch = 60 * 1;

        if ($minutes > 60 * 5) {
            $minutes = $minutes - $lunch;
        }
             
        return $minutes;
    }

    /**
     * Выбранная дата
     * @return void
     */
    protected function setDate()
    {
        $timeZone = \App\Setting::TIMEZONES[6];

        if ($this->argument('date')) {
            $this->date = Carbon::parse($this->argument('date'), $timeZone)->startOfDay();
        } else {
            $this->date = Carbon::now($timeZone)->startOfDay();
        }

        // $argUserId = $this->argument('user_id');
    }
    
}
