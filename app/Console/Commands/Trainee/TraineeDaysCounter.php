<?php

namespace App\Console\Commands\Trainee;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Analytics\UserStat;
use App\ProfileGroup;
use App\DayType;
use App\Timetracking;

class TraineeDaysCounter extends Command
{
	    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trainee:count_days {date?}';
    /**
     * 
     */
    protected $day;

    /**
     * 'Y-m-d'
     */
    protected $date;
        /**
     * 'Y-m-d'
     */
    protected $startOfMonth;

    public function handle() {
    	$date = $this->argument('date') ? Carbon::parse($this->argument('date')) : Carbon::now();
        $this->date = $this->argument('date') ? $this->argument('date') : date('Y-m-d');
        $this->day = $date->day;
        $this->startOfMonth = $date->startOfMonth()->format('Y-m-d');

    	$this->line('проверка');

    	$users = json_decode(ProfileGroup::find(48)->users);
    	foreach($users as $user){
    		$day_data = DayType::where('date',$this->date)->where('admin_id',$user)->whereIn('type', [DayType::DAY_TYPES['TRAINEE'], DayType::DAY_TYPES['RETURNED']])->get();
            $value = $this->getFirsts($day_data);

    		$this->saveASI([
	            'date' => $this->date,//$this->startOfMonth,
	            'employee_id' => $user,
	            'group_id' => 48,
	            'type' => 207 // 2й+ день
	        ], $value[1]);


            $this->saveASI([
                'date' => $this->date,//$this->startOfMonth,
                'employee_id' => $user,
                'group_id' => 48,
                'type' => 204 // 1й день
            ], $value[0]);

    	}

    }

    public function getFirsts($day_data){
        $first_day = 0;
        $second_day = 0;
        foreach($day_data as $data){
            $value = Timetracking::where('user_id',$data->user_id)->get();
            if($value->count() > 1){
                $second_day++;
            }else{
                $first_day++;
            }
        }
        $value = [$first_day, $second_day];
        return $value;
    }

    public function saveASI(array $fields, $value) {	        
        $date = Carbon::parse($fields['date'])->day($this->day)->format('Y-m-d');
        $us = UserStat::where([
            'date' => $date,
            'user_id' => $fields['employee_id'],
            'activity_id' => $fields['type'] 
        ])->first();

        if($us) {
            $us->value = $value;
            $us->save();
        } else {
            UserStat::create([
                'date' => $date,
                'user_id' => $fields['employee_id'],
                'activity_id' => $fields['type'],
                'value' => $value
            ]);
        }
    }
}