<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Analytics\UserStat;
use App\ProfileGroup;
use App\DayType;

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
    		$value = DayType::where('date',$this->date)->where('admin_id',$user)->where('type','>', 1)->get()->count();
    		$this->saveASI([
	            'date' => $this->date,//$this->startOfMonth,
	            'employee_id' => $user,
	            'group_id' => 48,
	            'type' => 207 // 2й день
	        ], $value);
    	}

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