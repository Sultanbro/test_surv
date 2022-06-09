<?php

/**
 * Класс устарел
 * Нигде не используется
 * 
 * 
 * 
 */
namespace App\Classes;

use App\User;
use App\AnalyticsSettings;
use App\AnalyticsSettingsIndividually as ASI;
use App\QualityRecordWeeklyStat;
use Carbon\Carbon;
use App\Models\Analytics\Activity;

class UserAnalytics 
{
    protected $user_id;
    public $date;

    public function __construct($user_id) {
        $this->user_id = $user_id;
        $this->date = Carbon::now()->startOfMonth()->format('Y-m-d');
    }

    /**
     * change date
     */
    public function setDate($date) {
        $this->date = Carbon::parse($date)->startOfMonth()->format('Y-m-d');
        return $this;
    }

    /**
     * Сформировать таблицу с показателями сотрудника
     */
    public function table() {
        
        $table = [];

        $user = User::find($this->user_id);
        if(!$user) return $table;

        
        // Узнать в каокй группе 
        $groups = $user->inGroups();
        $date = Carbon::parse($this->date);

        foreach ($groups as $key => $group) {
            $activities = Activity::where('group_id', $group->id)
               // ->whereNotIn('id', [55,56,57,58,59,60,61,62]) // OKK activities
                ->where('type', 'default') // not OKK activities
                ->get();
            
            $ignore_days = $group->workdays == 5 ?  [0,6] : [0]; 

            // нАЙТИ АКТИвности
            foreach ($activities as $activity) {

                $row = [];
                
                $workdays = workdays($date->year, $date->month, $ignore_days);

                $asi = ASI::where([
                    'date' => $this->date,
                    'group_id' => $group->id,
                    'type' => $activity->id,
                    'employee_id' => $this->user_id
                ])->first();

                $row['name'] = $activity->name;
                $row['plan'] = '';
                $row['fact'] = '';
                $row['daily_plan'] = $activity->daily_plan;
                $row['plan_unit'] = $activity->plan_unit;
                
                for ($i = 1; $i <= Carbon::parse($this->date)->daysInMonth; $i++) {
                    $row[$i] = '';
                }  

                // вытащить данные
                if($asi) {
                   
                    $data = json_decode($asi->data, true);

                    $row['plan'] = $activity->plan_unit == 'minutes' ? $activity->daily_plan * $workdays : $activity->daily_plan;
                    
                    $fact = 0;
                    $count = 0;

                    for ($i = 1; $i <= Carbon::parse($this->date)->daysInMonth; $i++) {
                        if(array_key_exists($i, $data)) {
                            $row[$i] = $data[$i];
                            $fact += (float)$data[$i];
                            $count++;
                        }
                    }  

                    if($activity->plan_unit == 'minutes') {
                        $row['fact'] = round($fact);
                    } else {
                        $row['fact'] = $count > 0 ? round($fact / $count, 2) : 0;
                    }
                } 

                array_push($table, $row);
            }

            
        }

   
        // Добавить контроль качества
        //foreach ($groups as $key => $group) {}
        array_push($table, $this->quality_row(0));
        
        return [
            'table' => $table,
            'fields' => $this->fields(),
        ];
    }

    /**
     * Сформировать заголовки для таблицы
     */
    private function fields() {
        $fields = [];

        
        $fields[] = [
            'label' => "Активность",
            'key'=> 'name',
            'variant'=> "title",
            'class'=> "text-left"
        ];
        $fields[] = [
            'label' => "План",
            'key'=> 'plan',
            'variant'=> "title",
            'class'=> "text-center"
        ];
        $fields[] = [
            'label' => "Факт",
            'key'=> 'fact',
            'variant'=> "title",
            'class'=> "text-center"
        ];
        
        for ($i = 1; $i <= Carbon::parse($this->date)->daysInMonth; $i++) {
            $fields[] = [
                'key' => ''. $i,
                'label'=> $i,
                'variant'=> "title",
                'class'=> "text-center"
            ];
        }   

        return $fields;
    }

    /**
     * Контроль качества
     */
    private function quality_row($group_id) {
        $row = [];

        $date = Carbon::parse($this->date);

        $grades = QualityRecordWeeklyStat::where([
            'month' => $date->month,
            'year' => $date->year,
            'user_id' => $this->user_id,
        ])->get();
        
        $row['name'] = "Оценка диалогов";
        $row['plan'] = 'План';
        $row['fact'] = 'Факт';

        for ($i = 1; $i <= $date->daysInMonth; $i++) {
            $q = $grades->where('day', $i)->first();
            $row[$i] = $q ? $q->total : '';
        }  
        
        return $row;
    }
    
}
