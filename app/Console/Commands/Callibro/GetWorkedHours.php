<?php

namespace App\Console\Commands\Callibro;

use App\User;
use App\Timetracking;
use App\TimetrackingHistory;
use App\ProfileGroup;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\AnalyticsSettings;
use App\AnalyticsSettingsIndividually;
use App\Classes\Analytics\Eurasian;
use App\Classes\Analytics\Kaztel;
use App\Classes\Analytics\HomeCredit;
use App\Models\Analytics\UserStat;
use App\Classes\Callibro;
use App\Models\CallibroDialer;

class GetWorkedHours extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'callibro:minutes_aggrees {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отработанное время сотрудников и согласия Евраз Home';
    
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


    
    protected $dialer;



    protected $group;

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
    public function handle() {

        $date = $this->argument('date') ? Carbon::parse($this->argument('date')) : Carbon::now();
        $this->date = $this->argument('date') ? $this->argument('date') : date('Y-m-d');
        $this->day = $date->day;
        $this->startOfMonth = $date->startOfMonth()->format('Y-m-d');

        $groups = [70];
        foreach($groups as $group_id) {
            $this->group = ProfileGroup::find($group_id);
            $this->dialer = CallibroDialer::where('group_id', $group_id)->first();

            $this->fetch($group_id);
            $this->line('Fetch completed for group_id: ' . $group_id);
        }

       // $this->saveHoursToTimetracking();
        
    }

    public function fetch($group_id) 
    {
        $users_ids = json_decode(ProfileGroup::find($group_id)->users);
        $users = User::whereIn('id', $users_ids)->get();

        foreach($users as $user) {
           // if($user->position_id != 32) continue; // Не оператор
            if($group_id == 70) { // Kaztel
            
                $minutes = Kaztel::getWorkedMinutes($user->email, $this->date);

                if($minutes == 0) continue; // Не записывать ноль
                if($minutes > 0 && $user->program_id == 1) {
                    $hours = Callibro::getWorkedHours($user->email, $this->date);
                  
                    if( $this->group && !in_array($user->id, $this->group->time_exceptions)) { // not in exception

                     //   dump($user->id, $user->last_name, $user->name, $this->group->time_exceptions);
                        $this->updateHours($user->id, $minutes, $hours);
                    }
                    
                }
                $aggrees = Kaztel::getAggrees($user->email, $this->date);
             
                $call_counts = Kaztel::getCallCounts($user->email, $this->date);

                $closed_cards = Kaztel::getClosedCards($this->date, $user->email);

                $this->saveASI([
                    'date' => $this->startOfMonth,
                    'employee_id' => $user->id,
                    'group_id' => $group_id,
                    'type' => 141 // минуты
                ], $minutes);
    
                $this->saveASI([
                    'date' => $this->startOfMonth,
                    'employee_id' => $user->id,
                    'group_id' => $group_id,
                    'type' => 137 // согласия
                ], $aggrees);

                $this->saveASI([
                    'date' => $this->startOfMonth,
                    'employee_id' => $user->id,
                    'group_id' => $group_id,
                    'type' => 134 // звонки от 10 секунд
                ], $call_counts);

                 $this->saveASI([
                    'date' => $this->startOfMonth,
                    'employee_id' => $user->id,
                    'group_id' => $group_id,
                    'type' => 152 // звонки от 10 секунд
                ], $closed_cards);

            }
        }

    }

    private function updateHours($user_id, $minutes, $worked_minutes) 
    {
   
        $date = $this->date;

        $timetracking = Timetracking::where('user_id', $user_id)
            ->whereDate('enter', $date)
            ->orderBy('enter', 'asc')
            ->first();

        
        if($this->dialer && $minutes >= $this->dialer->talk_minutes) {
            $minutes = $this->dialer->talk_hours * 60;
            $message = 'Изменено на ' . $this->dialer->talk_hours . ' часов. Выполнен план на ' . $this->dialer->talk_minutes . ' минут разговора';
        } else{ 
            $point = (int)($worked_minutes / 60);
            $minutes = $worked_minutes + $point * 5;
            $message = 'Отработано ' . $worked_minutes. ' минут. Добавлено ' . $point * 5 . ' минут';
        }

   

        if($timetracking) {
            if($timetracking->updated != 1) {
                $timetracking->total_hours = $minutes;
                $timetracking->updated = 2;
                $timetracking->save();
            } 
        } else {
            Timetracking::create([
                'enter' => Carbon::parse($date),
                'exit' => Carbon::parse($date),
                'total_hours' => $minutes,
                'updated' => 2,
                'user_id' => $user_id
            ]);
        }

        $th = TimetrackingHistory::where([
            'user_id' => $user_id,
            'author_id' => 5,
            'author' => 'Система ДВ',
            'date' => $date,
        ])->first();

        if($th) {
            $th->description = $message;
            $th->save();
        } else {
            TimetrackingHistory::create([
                'user_id' => $user_id,
                'author_id' => 5,
                'author' => 'Система ДВ',
                'date' => $date,
                'description' => $message,
            ]);
        }   
        
    }

    public function saveHoursToTimetracking() 
    {

        $users = User::where('program_id', 1)
            ->get();

        $this->line('Found ' . $users->count() . ' UCALLS users');

        $add_minutes = $this->getAdditionalMinutes(); // для home
        foreach ($users as $key => $user) {
            $total_hours = Callibro::getWorkedHours($user->email, $this->date);

            $tt = Timetracking::whereDate('enter', $this->date)
                ->where('user_id', $user->id)
                ->orderBy('enter', 'asc')
                ->first();

            if($tt) {
                if($tt->updated == 0 || $tt->updated == 3) {
                    $tt->total_hours = $total_hours;
                    $tt->program_id = 1;
                    $tt->updated = 3;
                    $tt->save();
                } 
            } else if($total_hours != 0) {
                
                Timetracking::create([
                    'total_hours' => $total_hours,
                    'user_id' => $user->id,
                    'enter' => Carbon::parse($this->date),
                    'exit' => Carbon::parse($this->date),
                    'program_id' => 1,
                    'updated' => 3,
                ]);
            }
        } 


    }

    public function saveASI(array $fields, $value) 
    {
        
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

    /**
     * За отсутсттвие связи inhouse Home
     */
    public function getAdditionalMinutes()
    {
        
    }
}
