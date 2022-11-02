<?php

namespace App\Console\Commands\Callibro;

use App\Timetracking;
use App\TimetrackingHistory;
use App\ProfileGroup;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Classes\Analytics\Eurasian;
use App\Models\Analytics\UserStat;
use App\Classes\Callibro;
use App\Models\CallibroDialer;
use App\Service\Department\UserService;

class GetWorkedHours extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'callibro:minutes_aggrees {date?} {fired?}';

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

    protected $currentUser;

    protected $currentDepartment;


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
    public function handle()
    {
        $this->setDate();
        $groups = [53];

        foreach($groups as $group_id) {
            $this->group  = ProfileGroup::find($group_id);
            $this->dialer = CallibroDialer::where('group_id', $group_id)->first();
            $this->currentDepartment = $group_id;

            $this->fetch($group_id);
            $this->line('Fetch completed for group_id: ' . $group_id);
        }
        
    }

    /**
     * Fetch data from callibro
     * 
     * every group has dialer 
     * 
     * dialers have script_ids
     * 
     * @return void
     */
    private function fetch($group_id) 
    {
        $users = $this->getUsers($group_id);

        foreach($users as $user) {
            
            $this->currentUser = $user->id;

            if($group_id == 53) { // Euras

                $args = [
                    $user->email,
                    $this->date,
                    [
                        'dialer_id' => 398,
                        'aggrees_scripts' => [2519]
                    ]
                ];

                $minutes = Callibro::getMinutes(...$args);

                if($minutes == 0) continue; // Не записывать ноль

                $aggrees         = Callibro::getAggrees(...$args);
                $correct_minutes = Callibro::getCallCounts(...$args);

                
                
                $this->saveUserStat(16, $minutes); // минуты
                $this->saveUserStat(18, $aggrees); // согласия
                $this->saveUserStat(208, $correct_minutes); // звонки от 10 секунд
                
                if($minutes > 0 && $user->program_id == 1) {
                    $hours = Callibro::getWorkedHours($user->email, $this->date);
                    $this->updateHours($user->id, $minutes, $hours);
                }

                //запишем посещения для Euras
                $startedDay = Callibro::startedDay($user->email, $this->date);
                    
                if($startedDay) {
                    $this->updateUserEnterTime($user->id, $startedDay);
                } 

            }

        }

    }
    
    /**
     * Set date
     * 
     * @return void
     */
    private function setDate() 
    {
        $date = $this->argument('date') ? Carbon::parse($this->argument('date')) : Carbon::now();
        $this->date = $this->argument('date') ? $this->argument('date') : date('Y-m-d');
        $this->day = $date->day;
        $this->startOfMonth = $date->startOfMonth()->format('Y-m-d');
    }

    /**
     * Get users in Department
     * 
     * @return Collection
     */
    private function getUsers($group_id) 
    {
        $users = (new UserService)->getEmployees(
            $group_id,
            Carbon::parse($this->date)->startOfMonth()->format('Y-m-d')
        ); 

        $users = collect($users);

        if($this->argument('fired')) {
            $fired = (new UserService)->getFiredEmployees(
                $group_id,
                Carbon::parse($this->date)->startOfMonth()->format('Y-m-d')
            ); 
            
            $users = $users->merge(collect($fired));
        }
        
        return $users;
    }

    /**
     * Update worked hours in Timetracking::class
     * 
     * @return void
     */
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
            $point   = (int)($worked_minutes / 60);
            $minutes = $worked_minutes; //+ $point * 5;
            //$message = 'Отработано ' . $worked_minutes. ' минут. Добавлено ' . $point * 5 . ' минут';
            $message = 'Отработано ' . $worked_minutes. ' минут.';
        }

        if($minutes > 480) {
            $minutes = 480;
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

    /**
     * Save user's activity value to UserStat::class
     * 
     * @return void
     */
    private function saveUserStat($activity_id, $value) 
    {
        $date = Carbon::parse($this->startOfMonth)->day($this->day)->format('Y-m-d');
        
        $us = UserStat::where([
            'date'        => $date,
            'user_id'     => $this->currentUser,
            'activity_id' => $activity_id
        ])->first();

        if($us) {
            $us->value = $value;
            $us->save();
        } else {
            UserStat::create([
                'date' => $date,
                'user_id' => $this->currentUser,
                'activity_id' => $activity_id,
                'value' => $value
            ]);
        }

    }

    /**
     * update enter in Timetracking::class
     * 
     * @return void
     */
    private function updateUserEnterTime($user_id, $enter)
    {
        $userInExceptions = ! ( $this->group && !in_array($user_id, $this->group->time_exceptions) );

        if($userInExceptions) return false;

        $timetracking = Timetracking::where('user_id', $user_id)
                ->whereDate('enter', $this->date)
                ->orderBy('enter', 'asc')
                ->first();


        if(!$timetracking) {

            Timetracking::create([
                'enter'       => $enter,
                'exit'        => Carbon::parse($this->date),
                'total_hours' => 0,
                'updated'     => 2,
                'user_id'     => $user_id
            ]);

        } else if($timetracking->updated != 1) {

            $timetracking->enter   = $enter;
            $timetracking->updated = 2;
            $timetracking->save();

        } 
    }


    
}
