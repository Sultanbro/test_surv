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

    protected $activities;

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

        $groups = $this->getGroupsWithConfig();

        foreach($groups as $group) {

            $group_id = $group['id'];

            $department  = ProfileGroup::find($group_id);

            if(!$department) continue;

            $group['time_exceptions'] = $department->time_exceptions;

            $this->group = $group;
           
            $this->dialer = CallibroDialer::where('group_id', $group_id)->first();

            $this->currentDepartment = $group_id;

            $this->fetch($group_id);
        }
        
    }

    /**
     * Fetch groups with config for callibro 
     * 
     * @return array
     */
    private function getGroupsWithConfig() 
    {
        return [
            [
                'id' => 53, //  Eurasian
                'activities' => [
                    'minutes' => 16,
                    'aggrees' => 18,
                    'correct_minutes' => 208,
                ],
                'dialer_id' => 398,
                'aggrees_scripts' => [2519],
                'time_exceptions' => []
            ],
        ];
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

            // prepare args for callibro methods
            $args = [
                $user->email,
                $this->date,
                [
                    'dialer_id'       => $this->group['dialer_id'],
                    'aggrees_scripts' => $this->group['aggrees_scripts'],
                ]
            ];

            $results = $this->getResults($args);

            // если есть минуты обновить часы в табели
            if($results['minutes'] > 0 && $user->program_id == 1) {
                $hours = Callibro::getWorkedHours($user->email, $this->date);
                $this->updateHours($user->id, $results['minutes'], $hours);
            }

            // время начала рабочего дня
            $startedDay = Callibro::startedDay($user->email, $this->date);
                
            if($startedDay) {
                $this->updateUserEnterTime($user->id, $startedDay);
            } 
        }

        $this->line('Fetch completed for group_id: ' . $group_id);
    }
    
    /**
     * Получаем Минуты согласия и звонки от 10 сек
     * и Cохраняем в активности в Аналитике - подробной
     * 
     * @param array $args 
     * @return array
     */
    private function getResults(array $args) 
    {   
        $results = [
            'minutes' => 0,
            'aggrees' => 0,
            'correct_minutes' => 0,
        ];

        $minutes = Callibro::getMinutes(...$args);

        // Не записывать ноль
        if($minutes == 0) return $results; 
        
        $aggrees         = Callibro::getAggrees(...$args);
        $correct_minutes = Callibro::getCallCounts(...$args);

        $this->saveUserStat($this->group['activities']['minutes'], $minutes); // минуты
        $this->saveUserStat($this->group['activities']['aggrees'], $aggrees); // согласия
        $this->saveUserStat($this->group['activities']['correct_minutes'], $correct_minutes); // звонки от 10 секунд

        return $results;
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

        $trainees = (new UserService)->getTrainees(
            $group_id,
            Carbon::parse($this->date)->startOfMonth()->format('Y-m-d')
        ); 

        $users = $users->merge(collect($trainees));

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
        $userInExceptions = in_array($user_id, $this->group['time_exceptions']);

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
