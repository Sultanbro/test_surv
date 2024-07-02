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
use App\User;

class FetchActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'callibro:fetch {date?} {fired?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отработанное время сотрудников и согласия Евраз Home';
    
    /**
     * day
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

    /**
     * dialer
     */
    protected $dialer;

    /**
     * user in loop
     */
    protected $currentUser;

    /**
     * group in loop
     */
    protected $currentDepartment;

    /**
     * group configs in loop
     */
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
//        $groups = ProfileGroup::getUcallsConnectedGroups()
//            ->toArray();

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

            //  Евразийский банк
            [
                'id' => 53,
                'activities' => [
                    'minutes' => 16,
                    'aggrees' => 18,
                    'correct_minutes' => 208,
                    'conversion' => 65
                ],
                'dialer_id' => 398,
                'aggrees_scripts' => [2519],
                'closed_card_scripts'  => [
                    2519, // Дата визита
                    2521, // В декрете
                    2529, // Низкий доход
                    2532, // Есть просрочка
                    2533, // Нет пенсионных отчислений
                    2534, // Инвалидность 1, 2 группы
                    2536, // Военные Алматы/Алматинская обл
                    2538, // Не интересует
                    2539, // Негатив к Банку
                    2540, // Не устраивают условия
                    2541, // Подумает
                    2542, // Интересует другой продукт (Автокредит)
                    2543, // Интересует другой продукт (Рефинансирование)
                    2544, // Интересует другой продукт (Ипотека)
                    2545, // Интересует другой продукт (Депозит)
                    2549, // Уточненный номер
                    2551, // Клиент умер
                    2552, // Не гражданин РК
                    12275, // Неверный номер
                    13015, // Согласился онлайн
                ],
                'time_exceptions' => []
            ],

            // Яндекс доставка
            // [
            //     'id' => 97,
            //     'activities' => [
            //         'minutes' => 217,
            //         'aggrees' => 219,
            //         'correct_minutes' => 222, 
            //         'conversion' => 224,
            //     ],
            //     'dialer_id' => 448,
            //     'aggrees_scripts' => [],
            //     'closed_card_scripts' => [],
            //     'time_exceptions' => [],
            // ],
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

            $results = $this->getResults([
                $user->email,
                $this->date,
                [
                    'dialer_id'           => $this->group['dialer_id'],
                    'aggrees_scripts'     => $this->group['aggrees_scripts'] ?? [],
                    'closed_card_scripts' => $this->group['closed_card_scripts'] ?? [],
                ]
            ]);

            $this->saveWorkedMinutesAndEnterTime($user, $results['minutes']);

        }

        $this->line('Fetch completed for group_id: ' . $group_id);
    }

    /**
     * Обновить отработанное время и время начала рабочего дня в табели в Timetracking::class
     * 
     * @param $user
     * @param int $minutes
     * @return array
     */
    private function saveWorkedMinutesAndEnterTime(User $user, int $minutes)
    {     
        // если есть минуты обновить часы в табели
        if($minutes > 0 && $user->program_id == 1) {

            $hours = Callibro::getWorkedHours($user->email, $this->date);
            
            $minutes = $this->updateHours($user->id, $minutes, $hours);
        }

        // время начала рабочего дня
        $startedDay = Callibro::startedDay($user->email, $this->date);
        
        $this->line($user->id .'= '.  $startedDay . ' = ' . $minutes);
        if($startedDay) {
            $this->updateUserEnterTime($user->id, $startedDay, $minutes);
        } 
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
            'conversion' => 0,
        ];
        dump($this->group['dialer_id']);
        $minutes = Callibro::getMinutes(...$args);

        // Не записывать ноль
        if($minutes == 0) return $results; 
        
        $aggrees         = Callibro::getAggrees(...$args);
        $correct_minutes = Callibro::getCallCounts(...$args);
        $closed_cards    = Callibro::getClosedCards(...$args);

        // расчет конверсии согласий на диалоги
        $conversion = 0; 

        if($closed_cards != 0) {
            $conversion = $aggrees / $closed_cards * 100;
            $conversion = number_format($conversion, 1);
        }

        // сохранить показатели
        $this->saveUserStat($this->group['activities']['minutes'], $minutes); // минуты
        $this->saveUserStat($this->group['activities']['aggrees'], $aggrees); // согласия
        $this->saveUserStat($this->group['activities']['correct_minutes'], $correct_minutes); // звонки от 10 секунд
        $this->saveUserStat($this->group['activities']['conversion'], $conversion); // конверсия согласий на диалоги

        $results = [
            'minutes' => $minutes,
            'aggrees' => $aggrees,
            'correct_minutes' => $correct_minutes,
            'conversion' => $conversion,
        ];

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
     * @return int
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
                'enter' => Carbon::parse($date)->subHours(6),
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
        
        return $minutes;
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
    private function updateUserEnterTime($user_id, $enter, $minutes)
    {
        $userInExceptions = in_array($user_id, 
            $this->group['time_exceptions']
            ? $this->group['time_exceptions']
            : []
        );

        if($userInExceptions) return false;

        $timetracking = Timetracking::where('user_id', $user_id)
                ->whereDate('enter', $this->date)
                ->orderBy('enter', 'asc')
                ->first();


        if(!$timetracking) {

            Timetracking::create([
                'enter'       => Carbon::parse($enter)->subHours(6),
                'exit'        => Carbon::parse($this->date),
                'total_hours' => $minutes,
                'updated'     => 2,
                'user_id'     => $user_id
            ]);

        } else if($timetracking->updated != 1) {

            $timetracking->enter   = $enter;
            $timetracking->updated = 2;
            $timetracking->total_hours = $minutes;
            $timetracking->save();

        } 
    }

    

    
}
