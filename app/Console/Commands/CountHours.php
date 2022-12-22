<?php

namespace App\Console\Commands;

use App\ProfileGroup;
use App\Timetracking;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
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
        
        $timetrackingRecords = $this->getRecords();

        foreach($timetrackingRecords as $record) {

            if( !$record->user ) {
                continue;
            }

            $schedule = $record->user->schedule();

            $workStart = $schedule['start'];
            $workEnd   = $schedule['end'];
            
          
            $timeStart = $this->countFromShiftStartTime($workStart, $record->enter);
            $timeEnd   = Carbon::parse($record->exit->setTimezone($record->user->timezone())); // не учитываем конец дня, засчитываем как переработку

            $this->line('start ' . $record->user->timezone());
            $this->line('start ' . $timeStart->timezone);
            $this->line('end   ' . $timeEnd->timezone);

            $minutes = $timeEnd->diffInMinutes($timeStart);

            $this->line('minutes ' . $timeEnd);

            $minutes = $this->subtractLunchTime($minutes);
            $minutes = $this->setMoreThanZero($minutes);

            $record->update(['total_hours' => $minutes]);
        }

        $this->line('Было найдено нередактированных записей: '. count($timetrackingRecords));
    }

    protected function countFromShiftStartTime(
        Carbon $workStartAt,
        Carbon $enterAt
    ) : Carbon
    {      
        // in User::schedule() subtracts 30 minutes. Here we add it to count worked hours correctly
        $workStartAt->addMinutes(30); 

        $workStartAt = $this->date->setTimeFrom($workStartAt);

        if ( $workStartAt->diffInMinutes($enterAt, false) > 0 ) {
            return $workStartAt;
        }
        
        return $enterAt;
    }

    protected function subtractLunchTime(int|float $minutes) : int|float
    {
        $lunch = 60 * 1;

        if ($minutes > 60 * 5) {
            $minutes = $minutes - $lunch;
        }
             
        return $minutes;
    }

    protected function setDate() : void
    {
        $timeZone = \App\Setting::TIMEZONES[6];

        if ($this->argument('date')) {
            $this->date = Carbon::parse($this->argument('date'), $timeZone)->startOfDay();
        } else {
            $this->date = Carbon::now($timeZone)->startOfDay();
        }
    }

    protected function getRecords() : Collection
    {
        $timetrackingRecords = Timetracking::query()
            ->with('user')
            ->select('id', 'enter', 'exit', 'total_hours', 'user_id')
            ->whereDate('enter',  $this->date->format('Y-m-d'))
            ->where('updated', 0);// non updated records

        if($this->argument('user_id')) {
            $timetrackingRecords->where('user_id', $this->argument('user_id'));
        }

        return $timetrackingRecords->get();
    }

    protected function setMoreThanZero(int|float $minutes) : int|float
    {
       return $minutes <= 0 ? 0 : $minutes;
    }
    
}
