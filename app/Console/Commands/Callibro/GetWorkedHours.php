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
use App\Classes\Analytics\HomeCredit;
use App\Models\Analytics\UserStat;
use App\Classes\Callibro;

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

        $groups = [53];
        foreach($groups as $group_id) {
            $this->fetch($group_id);
            $this->line('Fetch completed for group_id: ' . $group_id);
        }

        $this->saveHoursToTimetracking();
        
    }

    public function fetch($group_id) {

	    $users_ids = json_decode(ProfileGroup::find($group_id)->users);
        $users = User::whereIn('id', $users_ids)->get();

        foreach($users as $user) {
           // if($user->position_id != 32) continue; // Не оператор
            
            if($group_id == 53) { // Eurasian
                $minutes = Eurasian::getWorkedMinutes($user->email, $this->date);
                if($minutes == 0) continue; // Не записывать ноль
                if($minutes > 0 && $user->program_id == 1) {
                    $hours = Callibro::getWorkedHours($user->email, $this->date);
                    $this->updateHours($user->id, $minutes, $hours);
                }
                $aggrees = Eurasian::getAggrees($user->email, $this->date);

                $this->saveASI([
                    'date' => $this->startOfMonth,
                    'employee_id' => $user->id,
                    'group_id' => $group_id,
                    'type' => 16 // минуты
                ], $minutes);
    
                $this->saveASI([
                    'date' => $this->startOfMonth,
                    'employee_id' => $user->id,
                    'group_id' => $group_id,
                    'type' => 18 // согласия
                ], $aggrees);



            }

            if($group_id == 57) { // Home credit
                $minutes = HomeCredit::getWorkedMinutes($user->email, $this->date);
                if($minutes == 0) continue; // Не записывать ноль
                $aggrees = HomeCredit::getAggrees($user->email, $this->date);

                $this->saveASI([
                    'date' => $this->startOfMonth,
                    'employee_id' => $user->id,
                    'group_id' => $group_id,
                    'type' => 37 // минуты
                ], $minutes);
    
                $this->saveASI([
                    'date' => $this->startOfMonth,
                    'employee_id' => $user->id,
                    'group_id' => $group_id,
                    'type' => 38 // согласия
                ], $aggrees);
            }

        }

        /**
         * AS
         */
        if($group_id == 57) {
            
            $as = AnalyticsSettings::where([
                'group_id' => HomeCredit::ID,
                'date' => $this->startOfMonth
            ])->first();

            if($as && array_key_exists(HomeCredit::S_CLOSED_CARDS, $as->data)) {
                
                $closed_cards = HomeCredit::getClosedCards($this->date);

                $data = $as->data;
                $data[HomeCredit::S_CLOSED_CARDS][$this->day] = $closed_cards;
                $as->data = $data;
                $as->save();
            } else {
                $this->line('Не найден AS $group_id = ' . $group_id . ' Дата: ' . $this->startOfMonth);
            }
        }

        if($group_id == 53) {
            
            $as = AnalyticsSettings::where([
                'group_id' => Eurasian::ID,
                'date' => $this->startOfMonth
            ])->first();

            if($as && array_key_exists(Eurasian::S_CLOSED_CARDS, $as->data)) {
                
                $closed_cards = Eurasian::getClosedCards($this->date);

                $data = $as->data;
                $data[Eurasian::S_CLOSED_CARDS][$this->day] = $closed_cards;
                $as->data = $data;
                $as->save();
            } else {
                $this->line('Не найден AS $group_id = ' . $group_id . ' Дата: ' . $this->startOfMonth);
            }
        }
    }

    private function updateHours($user_id, $minutes, $worked_minutes) {
        
        $date = $this->date;

        $timetracking = Timetracking::where('user_id', $user_id)
            ->whereDate('enter', $date)
            ->orderBy('enter', 'asc')
            ->first();

        if($minutes >= 270) {
            $minutes = 480;
            $message = 'Изменено на 8 часов. Выполнен план на 270 минут разговора';
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

    public function saveHoursToTimetracking() {

        $users = User::where('program_id', 1)
            ->get();

        //$home_users = json_decode(ProfileGroup::find(57)->users);
        $this->line('Found ' . $users->count() . ' UCALLS users');

        $add_minutes = $this->getAdditionalMinutes(); // для home
        foreach ($users as $key => $user) {
            $total_hours = Callibro::getWorkedHours($user->email, $this->date);

            $tt = Timetracking::whereDate('enter', $this->date)
                ->where('user_id', $user->id)
                ->orderBy('enter', 'asc')
                ->first();

            
            // if(in_array($user->id, $home_users) && $total_hours < 540) {
            //     $total_hours = $total_hours + (int)$add_minutes;
            //     if($total_hours > 540) $total_hours = 540;
            // }

            if($tt && $user->id == 3173) {
                $total_hours = 540;
            }

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

    public function saveASI(array $fields, $value) {
        $asi = AnalyticsSettingsIndividually::where($fields)->first();

        if($asi) {
            $data = json_decode($asi->data, true);
            $data[$this->day] = $value;
            $asi->data = json_encode($data);
            $asi->user_id = 0;
            $asi->group_id = $fields['group_id'];
            $asi->save();
        } else {
            AnalyticsSettingsIndividually::create([
                'date' => $fields['date'],
                'employee_id' => $fields['employee_id'],
                'user_id' => 0,
                'group_id' => $fields['group_id'],
                'data'=> json_encode([$this->day => $value]),
                'type' => $fields['type'] // минуты
            ]);
        }

        // User stat New analytics

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
    public function getAdditionalMinutes() {
        $settings = AnalyticsSettings::where('date', Carbon::parse($this->date)->day(1)->format('Y-m-d'))
            ->where('group_id', 57)
            ->where('type', 'basic')
            ->first();

        $minutes = 0;
        $index = 11;
        if($settings && array_key_exists($index, $settings->data) && array_key_exists($this->day, $settings->data[$index])) {
            $minutes = (int)$settings->data[$index][$this->day];
        }
        
        return $minutes;
    }
}
