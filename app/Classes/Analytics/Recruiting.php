<?php
namespace App\Classes\Analytics;

use DB;
use App\User;
use App\UserDescription;
use App\Trainee;
use App\DayType;
use App\ProfileGroup;
use App\AnalyticsSettings;
use App\AnalyticsSettingsIndividually;
use App\Models\Analytics\RecruiterStat;
use App\Classes\Analytics\LayoffQuiz;
use Carbon\Carbon;
use App\External\Bitrix\Bitrix;
use App\Models\Bitrix\Lead;
use App\Models\Admin\History;
use App\UserNotification;
use App\ProfileGroupUser as PGU;
use App\UserAbsenceCause;

class Recruiting 
{
    public $table1 = 'analytics_settings';
    public $table2 = 'analytics_settings_individually';

    /**
     * Группа рекрутинг
     */
    CONST GROUP_ID = 48;

    /**
     * Поля сводной таблицы
     */
    CONST S_CREATED = 0; // Создано новых лидов за день
    CONST S_CALLS_OUT = 1; // ИСХ успешные соединения
    CONST S_CALLS_OUT_10 = 2; // Успешные соединения от 10 сек
    CONST S_CALLS_IN = 3; // ВХ успешные соединения
    CONST S_CALLS_MISSED = 4; // Пропущенные звонки
    CONST S_FAILED = 5; // Забраковано Лидов
    CONST S_PROCESSED = 6; // Обработанные лиды FAILED + CONVERTED
    CONST S_ONLINE = 7; // Количество рекрутеров на линии
    CONST S_CONVERTED_CONVERSION = 8; // Конверсия сконвертированных от обработанных
    CONST S_CONVERTED = 9; // Сконвертировано Лидов
    CONST S_EMPTY7 = 11;
    CONST S_EMPTY8 = 12;
    CONST S_TRAINING_TODAY = 13; // Стажируются сегодня
    CONST S_EMPTY11 = 14;
    CONST S_APPLIED = 15; // Приняты на работу
    CONST S_FIRED = 16; // Уволены


    /**
     * Поля индивидуальной таблицы
     */
    CONST I_CALL_PLAN = 0; // План наборов с ожиданием 7 гудков
    CONST I_CALLS_OUT = 1; // Успешные исходящие
    CONST I_FIRST_CALL = 2; // Время первого звонка
    CONST I_LAST_CALL = 3; // Время последнего звонка
    CONST I_CALLS_IN = 4; // Обработано успешных входящих
    CONST I_CALLS_MISSED = 5; // Пропущенные звонки
    CONST I_CONVERTED = 6; // Сконвертировано
    CONST I_APPLIED = 7; // Принято на работу

    /**
     * Поля чатбота
     */
    CONST B_CREATED = 0; // Создано новых лидов за день
    CONST B_FAILED = 1; // Забраковано Лидов
    CONST B_CONVERTED = 2; // Сконвертировано


    public static function defaultSummaryTable() {
        return [
            ['headers' => 'Создано новых лидов за день', 'fact' => 0,'plan' => 100,'conversion' => 0,],
            ['headers' => 'Наборы','fact' => 0,'plan' => 100,'conversion' => 0,],
            ['headers' => 'Успешные соединения от 10 сек','fact' => 0,'plan' => 100,'conversion' => 0,],
            ['headers' => 'ВХ успешные соединения','fact' => 0,'plan' => 50,'conversion' => 0,],
            ['headers' => 'Пропущенные','fact' => 0,'plan' => 0,'conversion' => 0,],
            ['headers' => 'Забраковано Лидов','fact' => 0,'plan' => 20,'conversion' => 0,],
            ['headers' => 'Обработано','fact' => 0,'plan' => 10,'conversion' => 0,],
            ['headers' => 'Рекрутеры на линии','fact' => 0,'plan' => 10,'conversion' => 0,],
            ['headers' => 'Конверсия','fact' => 0,'plan' => 10,'conversion' => 0,],
            ['headers' => 'Сконвертировано','fact' => 0,'plan' => 10,'conversion' => 0,],
            ['headers' => '',],
            ['headers' => '',],
            ['headers' => '',],
            ['headers' => 'В общем стажируются','fact' => 0,'plan' => 10,'conversion' => 0,],
            ['headers' => ''],
            ['headers' => 'Приняты в BP','fact' => 0,'plan' => 10,'conversion' => 0,],
            ['headers' => 'Уволились в этом месяце','fact' => 0,'plan' => 1,'conversion' => 0,],
        ];
    }

    public function defaultUserTable(int $user_id)
    {
        $user = User::withTrashed()->find($user_id);
        if($user) {
            return [
                'name' => $user->name . ' ' . $user->last_name,
                'id' => $user->id,
                'records' => [
                    ['headers' => 'План наборов с ожиданием 7 гудков','plan' => '200','fact' => '0','conversion' => '',], 
                    ['headers' => 'Успешные исходящие','plan' => '200','fact' => '0','conversion' => '',],
                    ['headers' => 'Время первого звонка','plan' => '09:30','fact' => '0','conversion' => '',],
                    ['headers' => 'Время последнего звонка','plan' => '18:00','fact' => '0','conversion' => '',],
                    ['headers' => 'Обработано успешных входящих','plan' => '','fact' => '0','conversion' => '',],
                    ['headers' => 'Пропущенные звонки','plan' => '','fact' => '0','conversion' => '',],
                    ['headers' => 'Сконвертировано','plan' => '20','fact' => '0','conversion' => '',],
                    ['headers' => 'Принято на работу','plan' => '3','fact' => '0','conversion' => '',],
                ],
            ];
        } else {
            return [
                'name' > $user_id,
                'id' => $user_id,
                'records' => []
            ];
        }
        
    }

    public static function defaultChatbotTable()
    {
        return [
            'name' => 'Чатбот',
            'id' => 0,
            'records' => [
                ['headers' => 'Создано новых лидов за день','plan' => '200','fact' => '0','conversion' => '0',],
                ['headers' => 'Забраковано Лидов','plan' => '0','fact' => '0','conversion' => '0',],
                ['headers' => 'Сконвертировано','plan' => '100','fact' => '0','conversion' => '0',],
            ],
        ];
        
        
    }

    /**
     * 
     * $month  Carbon 
     * @return void
     */
    public function updateSummaryTable($date) {
        
        $table = self::getSummaryTable($date);

        $data = $this->sumFacts($table->data, $date);
        

        return ;

    }

    /**
     * 
     * $date  Carbon 
     * @return AnalyticsSettings
     */
    public static function getSummaryTable($date) {
        $table = AnalyticsSettings::whereDate('date', $date)
            ->where('group_id', self::GROUP_ID)
            ->where('type', 'basic')
            ->first();
        
        return $table;
    }


    /**
     * 
     * $table  AnalyticsSettings 
     * @return void
     */
    public function sumFacts($arr, $date) {

        $arr[self::S_CREATED]['fact'] = 0; // Создано новых лидов за день
        $arr[self::S_CALLS_OUT]['fact'] = 0; // ИСХ успешные соединения
        $arr[self::S_CALLS_OUT_10]['fact'] = 0; // ИСХ успешные соединения
        $arr[self::S_CALLS_IN]['fact'] = 0; // ВХ успешные соединения
        $arr[self::S_CALLS_MISSED]['fact'] = 0; // Пропущенные
        $arr[self::S_FAILED]['fact'] = 0; // Забраковано Лидов

        $arr[self::S_CONVERTED]['fact'] = 0; // Сконвертировано

        $arr[self::S_TRAINING_TODAY]['fact'] = 0; // В общем стажируются

        $arr[self::S_APPLIED]['fact'] = 0; // Приняты в BP
        $arr[self::S_FIRED]['fact'] = 0; // Уволились в этом месяце
        



        $arr = $this->sumCommons($arr, $date);
        $arr = $this->sumIndividuals($arr, $date);
        //$arr = $this->sumBotIndexes($arr, $date);

        $arr = $this->sumOnline($arr, $date);

        $arr = $this->sumTrainees($arr, $date);
        $arr = $this->sumApplies($arr, $date);
        $arr = $this->sumFires($arr, $date);

        $arr = $this->sumProcessed($arr, $date);
        
        return $arr;
    }



    /**
     * @return String
     */
    public static function getLastDay($date) {
        return $date->month == date('m') ? date('d') : $date->daysInMonth;
    }

    /**
     * 
     * $arr   array
     * $date  Carbon month
     * @return void
     */
    public function sumCommons($arr, $date) {
     
        for ($i = 1; $i <= $date->daysInMonth; $i++) {
                
            $arr[self::S_CREATED]['fact'] += array_key_exists($i, $arr[self::S_CREATED]) ? (int)$arr[self::S_CREATED][$i] : 0;
            //$arr[4]['fact'] += array_key_exists($i, $arr[4]) ? (int)$arr[4][$i] : 0;
            // $arr[6]['fact'] += array_key_exists($i, $arr[6]) ? 0 : 0;

        }
        
        return $arr;
    }

    /**
     * 
     * $arr   array
     * $date  Carbon month
     * @return void
     */
    public function sumOnline($arr, $date) {

        $arr[self::S_ONLINE]['fact'] = 0;
        $arr[self::S_ONLINE]['headers'] = 'Рекрутеры на линии';

        $last_day = $date->daysInMonth;
        if($date->format('m') == date('m') && $date->format('Y') == date('Y')) {
            $last_day = (int)date('d');
        }

        for ($i = 1; $i <= $last_day; $i++) {
            $value = $this->getOnlineRates($date->day($i)->format('Y-m-d'));

            $arr[self::S_ONLINE][$i] = $value;
            $arr[self::S_ONLINE]['fact'] += $value;
        }
        
        $date = $date->startOfMonth();
        return $arr;
    }


    /**
     * Получить количество ставки на конкретный день
     * $date  format('Y-m-d')
     * @return void
     */
    public function getOnlineRates($date) {
        $tabu_users = [5263]; // Те, кого не надо учитывать

        $records = RecruiterStat::where('date', $date)
            ->where(function($query){
                $query->where('calls', '>', 0)
                    ->orWhere('minutes', '>', 0)
                    ->orWhere('converts', '>', 0);
            })
            ->get()
            //->where('total', '!=', '0')
            ->whereNotIn('user_id', $tabu_users);
            
        $user_ids = [];
        $users = $records->pluck('user_id')->toArray();
        $users = array_unique($users);

        foreach($users as $user_id) {
            $calls = $records->where('user_id', $user_id)->sum('calls');

            if($calls >= 3) {
                array_push($user_ids, $user_id);
            }
        }

        $value = 0;
        foreach($user_ids as $user_id) {
            $user = User::withTrashed()->find($user_id);
            if($user) {
                $value += $user->full_time == 1 ? 1 : 0.5;
            }
        }
        
        return $value;
    }

    /**
     * $arr   array
     * $date  Carbon month
     * @return void
     */
    public function sumBotIndexes($arr, $date) {
        $asi = AnalyticsSettingsIndividually::whereDate('date', $date)
                    ->where('group_id', self::GROUP_ID)
                    ->where('employee_id', 0)
                    ->first();

        if($asi) {
            $data = json_decode($asi->data,true);

            if(!array_key_exists(self::S_FAILED, $arr)) { 
                $arr[self::S_FAILED]['fact'] = 0;
            }
           
            if(array_key_exists(self::B_FAILED, $data)) {
                for ($i = 1; $i <= $date->daysInMonth; $i++) {
                    $x = array_key_exists($i, $data[self::B_FAILED]) ? $data[self::B_FAILED][$i] : 0;
                    $arr[self::S_FAILED][$i] = $x;
                    $arr[self::S_FAILED]['fact'] += $x;
                }  
            } 
            
        }

        return $arr;
    }   

    /**
     * 
     * $arr   array
     * $date  Carbon month
     * @return void
     */
    public function sumIndividuals($arr, $date) {
     
        $asi = AnalyticsSettingsIndividually::whereDate('date', $date)
                    ->where('group_id', self::GROUP_ID)
                    ->where('employee_id', '!=', 0)
                    ->get();
   
        if($asi->count() > 0) {
            for ($i = 1; $i <= $date->daysInMonth; $i++) {
        
                $count = 0;  // План наборов с ожиданием 7 гудков
                $count2 = 0; //  успешных ИСХ ОТ 10 с
                $count4 = 0; // Обработано успешных входящих
                $count5 = 0; // Пропущенные
                $count6 = 0; // Сконвертировано
                
             
                foreach($asi as $asi_user) {
                    
                    $data = json_decode($asi_user->data, true);
                  
                    if(array_key_exists($i, $data[self::I_CALL_PLAN])) $count += (int)$data[self::I_CALL_PLAN][$i];
                    if(array_key_exists($i, $data[self::I_CALLS_OUT])) $count2 += (int)$data[self::I_CALLS_OUT][$i];
                    if(array_key_exists($i, $data[self::I_CALLS_IN])) $count4 += (int)$data[self::I_CALLS_IN][$i];
                    if(array_key_exists($i, $data[self::I_CALLS_MISSED])) $count5 += (int)$data[self::I_CALLS_MISSED][$i];
                    if(array_key_exists($i, $data[self::I_CONVERTED])) $count6 += (int)$data[self::I_CONVERTED][$i];
                
                }
                
                
                if($count != 0) {
                    $arr[self::S_CALLS_OUT][$i] = $count;
                    $arr[self::S_CALLS_OUT]['fact'] += $count;
                } 

                if($count != 0) {
                    $arr[self::S_CALLS_OUT_10][$i] = $count2;
                    $arr[self::S_CALLS_OUT_10]['fact'] += $count2;
                } 
    
                if($count4 != 0) {
                    $arr[self::S_CALLS_IN][$i] = $count4;
                    $arr[self::S_CALLS_IN]['fact'] += $count4;
                } 
    
                if($count5 != 0) {
                    $arr[self::S_CALLS_MISSED][$i] = $count5;
                    $arr[self::S_CALLS_MISSED]['fact'] += $count5;
                } 
    
                if($count6 != 0) {
                    $arr[self::S_CONVERTED][$i] = $count6;
                    $arr[self::S_CONVERTED]['fact'] += $count6;
                } 

    
            }       
        }

          

        return $arr;
    }   


    public function sumTrainees($arr, $date) {
        $_i = self::getLastDay($date);

        for ($i = 1; $i <= $_i; $i++) {
            $day = $date;
            $trainees = DayType::where('date', $day->day($i)->format('Y-m-d'))
                ->whereIn('type', [5,7])
                ->get()
                ->pluck('user_id')
                ->toArray();

            $trainees = array_unique($trainees);

            $c = count($trainees);
          
            if($c > 0) {
                $arr[self::S_TRAINING_TODAY][$i] = $c;
                $arr[self::S_TRAINING_TODAY]['fact'] += $c;
            }
        }
        return $arr;
    }

    public function sumApplies($arr, $date) {
        $_i = self::getLastDay($date);

        for ($i = 1; $i <= $_i; $i++) {
            
            // Приняты в BP
            $applied_users_with = \DB::table('users')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->whereYear('ud.applied', $date->year)
                ->whereMonth('ud.applied', $date->month)
                ->whereDay('ud.applied', $i)
                ->where('is_trainee', 0)
                ->get();

            $count_app = 0;
            foreach($applied_users_with as $auser) {
                
                $count_app += $auser->full_time == 1 ? 1 : 0.5;
            }

            
            // $applied_users_without = User::withTrashed()
            //     
            //     ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            //     ->whereYear('users.created_at', $date->year)
            //     ->whereMonth('users.created_at', $date->month)
            //     ->whereDay('users.created_at', $i)
            //     ->get([
            //         'users.full_time as full_time',
            //         'ud.id as tid',
            //         'ud.applied as applied',
            //     ]);

            // foreach($applied_users_without as $auser) {
            //     //if(is_null($auser->tid)) {
            //     $count_app += $auser->full_time == 1 ? 1 : 0.5;
            //   //  } 
            // }

            if($count_app > 0) {
                $arr[self::S_APPLIED][$i] = array_key_exists($i, $arr[self::S_APPLIED]) ? $count_app : 0;
                $arr[self::S_APPLIED]['fact'] += $count_app;
            }
        }

        return $arr;
    }
    
    public function sumFires($arr, $date) {
        $_i = self::getLastDay($date);

        for ($i = 1; $i <= $_i; $i++) {
            // Удалить после 11.12.2021
            // $trainees = Trainee::whereNull('applied')->pluck('user_id')->toArray();
            // $fired = User::withTrashed()
            //     ->whereNotIn('id', $trainees)
            //     ->whereDate('deleted_at', $date->day($i)->format('Y-m-d'))
            //     ->get();
            
            $fired = \DB::table('users')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('ud.is_trainee', 0)
                ->whereDate('deleted_at', $date->day($i)->format('Y-m-d'))
                ->get();

            $f_count = 0;
        
            foreach($fired as $f_user) {
                //dump($f_user->id);
                $f_count += $f_user->full_time == 1 ? 1 : 0.5;
            }
            
            if($f_count > 0) {
                $arr[self::S_FIRED][$i] = $f_count;
                $arr[self::S_FIRED]['fact'] += $f_count;
            }
        }

        return $arr;
    }



    public function sumProcessed($arr, $date) {
        $_i = self::getLastDay($date);

    
        $arr[self::S_PROCESSED]['fact'] = 0;
        for ($i = 1; $i <= $_i; $i++) {
            $conv = array_key_exists($i, $arr[self::S_CONVERTED])? $arr[self::S_CONVERTED][$i] : 0;
            $failed = array_key_exists($i, $arr[self::S_FAILED])? $arr[self::S_FAILED][$i] : 0;
            
            $arr[self::S_PROCESSED][$i] = (int)$conv + (int)$failed;
            $arr[self::S_PROCESSED]['fact'] += $arr[self::S_PROCESSED][$i];

            if($arr[self::S_PROCESSED][$i] != 0) {
                $conversion = ((int)$conv / $arr[self::S_PROCESSED][$i]) * 100;
                $conversion = number_format((float)$conversion, 2, '.', '');
            } else {
                $conversion = 0;
            }
            
            $arr[self::S_CONVERTED_CONVERSION][$i] = $conversion . '%';
        }
       
        return $arr;
    }

    /** 
     * Расчет колво требуемых сотрудников 
     */
public function planRequired($arr) {
        $groupsForCount = ProfileGroup::whereIn('id',[31,42,63,79,70,71])->get();
      
        $required = 0;
        foreach($groupsForCount as $group){
            $group_count = 0;
            $my_users =DB::table('users')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->whereIn('users.id', json_decode($group->users))
                ->whereNull('users.deleted_at')
                ->where('ud.is_trainee', 0)
                ->whereNull('ud.fire_date')
                ->get()
                ->pluck('id')
                ->toArray();
                dump(count($my_users));
                $required += ($group->required - count($my_users));
            
        }
        $arr[self::S_APPLIED]['plan'] = $required;//$groupsForCount->sum('required') - $count_working_users;

        return $arr;
    }
    

    /**
     * Получить допики на текущий момент
     * @return array
     */
    public function getExtra() {
        return [
            'working' => self::getWorkerQuantity()
        ];
    }
    
    /**
     * Получить колво работающих на сегодняшний день
     */
    public static function getWorkerQuantity($date = null) {
        
        if($date) {
            $settings = AnalyticsSettings::where([
                'date' => $date->format('Y-m-d'),
                'type' => 'basic',
                'group_id' => self::GROUP_ID,
            ])->first();
                
                $x_count = $settings && array_key_exists('working', $settings->extra ?? []) ? $settings->extra['working'] : 0;
            } else {
               // $x_trainees = Trainee::whereNull('applied')->get()->pluck('user_id')->toArray();
                
                $x_users = \DB::table('users')
                    ->whereNull('deleted_at')
                    ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                    ->where('ud.is_trainee', 0)
                    ->get();
                
                $x_count = 0;
                foreach ($x_users as $x_user) {
                    $x_count += $x_user->full_time == 1 ? 1 : 0.5;
                } 
                
            }
            
            return $x_count;
        }
        
    /**
     * Таблица с ответами из анкеты уволенных
     */
    public static function getQuizTable(Carbon $date) {
        $answers = [];
        $xarr = [];
       // $quizes = UserDescription::whereNotNull('quiz_after_fire')->get();
        
        $start = $date->startOfMonth()->format('Y-m-d');
        $end = $date->endOfMonth()->format('Y-m-d');

       
        $quizes = \DB::table('users')
                ->whereNotNull('deleted_at')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('quiz_after_fire', '!=', '[]')
                ->whereDate('deleted_at', '>=', $start)
                ->whereDate('deleted_at', '<=', $end)
                ->where('is_trainee', 0)
                ->get();
             
        $quiz = [];
        foreach ($quizes as $key => $quizz) {
            for($i=1;$i<=9;$i++) {
                $quiz_after_fire = json_decode($quizz->quiz_after_fire, true);
                if(array_key_exists($i, $quiz_after_fire)) {
                    $xarr[$i][] = $quiz_after_fire[$i];
                } 
            }
        }
        
        $quiz = LayoffQuiz::getQuestions();
        
        
        for ($x = 1; $x <= count($quiz); $x++) {
            $a = [];
            
            if(array_key_exists($x, $xarr)) {
                
                if($quiz[$x]['type'] == 'star') {
                    $sum = 1;
                    $count = 0;
                    foreach($xarr[$x] as $answer) {
                        if(in_array((int)$answer, [1,2,3,4,5,6,7,8,9,10])) {
                            $sum += (int)$answer;
                            $count++;
                        }
                    }
                    
                    $quiz[$x]['answers'][0]['count'] = $count;
                    
                    $avg = $count != 0 ? number_format($sum / $count, 0) : 1;
                    $quiz[$x]['answers'][0]['text'] = $avg;
                    $quiz[$x]['answers'][0]['percent'] =  number_format($avg  / 10 * 100, 0);
                    
                } else if($quiz[$x]['type'] == 'variant') {
                    foreach($xarr[$x] as $answer) {
                        $is_another = true;
                        for($i=0;$i<count($quiz[$x]['answers']);$i++) { // считаем количество ответов по вариантам
                            if($quiz[$x]['answers'][$i]['text'] == $answer) {
                                $quiz[$x]['answers'][$i]['count'] += 1; 
                                $is_another = false;
                                break;
                            } 
                        }
                        if($is_another) { // другой вариант
                            $quiz[$x]['answers'][count($quiz[$x]['answers']) - 1]['count'] += 1;
                        }
                    }
                    
                    $count = count($xarr[$x]); // количество ответов
                    for($i=0;$i<count($quiz[$x]['answers']);$i++) {
                        $quiz[$x]['answers'][$i]['percent'] = number_format($quiz[$x]['answers'][$i]['count'] / $count * 100, 0);
                    }
                } else if($quiz[$x]['type'] == 'answer') {
                    $quiz[$x]['answers'] = [];
                    foreach($xarr[$x] as $answer) {
                        array_push($quiz[$x]['answers'], [
                            'text' => $answer
                        ]);
                    }
                    
                }
                
            }
        }


        foreach($quiz as $key => $question) {
            if($key == 1) $quiz[$key]['order'] = 1; 
            if($key == 2) $quiz[$key]['order'] = 3; 
            if($key == 3) $quiz[$key]['order'] = 5; 
            if($key == 4) $quiz[$key]['order'] = 7; 
            if($key == 5) $quiz[$key]['order'] = 2; 
            if($key == 6) $quiz[$key]['order'] = 4; 
            if($key == 7) $quiz[$key]['order'] = 6; 
            if($key == 8) $quiz[$key]['order'] = 8; 
        }

        $_sort = array_column($quiz, 'order');
        array_multisort($_sort, SORT_ASC, $quiz); 

       
        return $quiz;
    }


    /**
     * Таблица Recruiting user
     * @param int $user_id
     * @param String $date 'Y-m-d'
     * @return array
     */ 
    public static function getIndicators($user_id, $date) {

        $start = Carbon::parse($date)->startOfMonth();
        $end = Carbon::parse($date)->endOfMonth();

        $asi  = AnalyticsSettingsIndividually::where('date',$start->format('Y-m-d'))
                    ->where('group_id', self::GROUP_ID)
                    ->where('employee_id', $user_id)
                    ->first();
            
        $user = User::withTrashed()->find($user_id);
        if(!$user) return [];

        if($asi) {
            $data = json_decode($asi->data, true);

            /////////////////// Count remain days
            $start = Carbon::now()->setDate($start->year, $start->month, 1);
            $end = Carbon::now()->setDate($start->year, $start->month, 1)->endOfMonth();
            
            $holidays = [
            //     Carbon::create(2014, 2, 2),
            ];

            
            if($end->timestamp - $start->timestamp >= 0 && $end->month >= $start->month) {
                $workDays = $start->diffInDaysFiltered(function (Carbon $date) use ($holidays) {
                    return !$date->isDayOfWeek(Carbon::SUNDAY); //&& !in_array($date, $holidays);
                }, $end);
            } else {
                $workDays = 0;
            }

            /////////////////

            $arr = [];
            
            // count totals
            $calls = 0;
            $converted = 0;
            $applied = 0;

            for($i = 1;$i<=31;$i++) {
               // dump($data[self::I_CALL_PLAN]);
                if(array_key_exists($i, $data[self::I_CALL_PLAN])) {
                    $calls += (int)$data[self::I_CALL_PLAN][$i];
                }
                if(array_key_exists($i, $data[self::I_CONVERTED])) {
                    $converted += (int)$data[self::I_CONVERTED][$i];
                }
                if(array_key_exists($i, $data[self::I_APPLIED])) {
                    $applied += (int)$data[self::I_APPLIED][$i];
                }
            }
            
            // $arr
            $arr['name'] = $user->name . ' ' . $user->last_name;
            $arr['out']['value'] = $calls; //Исх 
            $arr['out']['plan'] = $data[self::I_CALL_PLAN]['plan'] ? $data[self::I_CALL_PLAN]['plan'] * $workDays : 0;
            $arr['out']['percent'] = isset($data[self::I_CALL_PLAN]['plan']) ? number_format((float)($calls / ($data[self::I_CALL_PLAN]['plan'] * $workDays)) * 100, 1, '.', '') : 10; 
            $arr['calls'] = isset($data[self::I_CALL_PLAN]['plan']) ? number_format((float)($calls / ($data[self::I_CALL_PLAN]['plan'] * $workDays)) * 100, 1, '.', '') : 10;
            $arr['converted']['value'] = $converted; //Сконвертировано
            $arr['converted']['plan'] = $data[self::I_CONVERTED]['plan'] ? $data[self::I_CONVERTED]['plan'] * $workDays : 0;
            $arr['converted']['percent'] = isset($data[self::I_CONVERTED]['plan']) ? number_format((float)($converted / ($data[self::I_CONVERTED]['plan'] * $workDays)) * 100, 1, '.', '') : 10;
            $arr['applied']['value'] = $applied; //Принято
            $arr['applied']['plan'] = $data[self::I_APPLIED]['plan'] ? $data[self::I_APPLIED]['plan'] * $workDays : 0;
            $arr['applied']['percent'] = isset($data[self::I_APPLIED]['plan']) ? number_format((float)($applied / ($data[self::I_APPLIED]['plan'] * $workDays)) * 100, 1, '.', '') : 10;


            return $arr;
        } else {
            return [];
        }
    }

    public static function transferTraining($user_id, $date, $time) {

        // перенос стадии сделки в битриксе
        // время собеседования или обучения в битриксе инхаус / ремоут
        // в скайпах стажер поменять дату приглашения
        // записать в историю

        $lead = Lead::where('user_id', $user_id)->first();
        $bitrix = new Bitrix();
        $datetime = $date . ' ' . $time . ':00';

        if($lead) {

            if($lead->skyped) {
                $field = 'UF_CRM_1568000119'; //  Время обучения
                $stage = 'C4:FINAL_INVOICE'; // Remote согласие на обучение
            } else{
                $field = 'UF_CRM_1633579757'; // Время стажировки (штатный)
                $stage = 'C4:27'; // Inhouse на собеседование
            }

            if($lead->deal_id != 0) {
                $deal_id = $lead->deal_id;
            } else {
                $deal_id = $bitrix->findDeal($lead->lead_id, false);
            }
            
            if($deal_id == 0) {
                return 'DealNotFound'; 
            }

            $bitrix->changeDeal($lead->deal_id, [
                $field => $datetime, 
                'STAGE_ID' => $stage, // Стадия сделки: Обучается
            ]);
           

            if($lead->invite_at)  {
                $daytype = DayType::where([
                    'user_id' => $user_id,
                    'type' => DayType::DAY_TYPES['TRAINEE'],
                    'date' => Carbon::parse($lead->invite_at)->format('Y-m-d'), 
                ])->first();
    
                if($daytype) $daytype->delete();

                $daytype = DayType::create([
                    'user_id' => $user_id,
                    'type' => DayType::DAY_TYPES['TRAINEE'],
                    'date' => Carbon::parse($datetime)->format('Y-m-d'), 
                    'admin_id' => 5, // Али Суперюзер
                ]);
            }
            
            
            
           

            // ====================

            $lead->invite_at = Carbon::parse($datetime);
            $lead->save();

            // ===================
            return 1; 
        } else {
            History::user(\Auth::user()->id, 'Перенос обучения', [
                'error' => 'Не найден лид',
                'lead' => $lead,
                'data' => $request->all(),
            ]);
            return 'LeadNotFound'; 
        }


        
    }


    public static function user($user_id, String $action, $data = []) {
        self::create([
            'author' => self::USER,
            'author_id' => $user_id,
            'action' => $action,
            'data' => $data,
        ]);
    }


    /**
     * Получить подробные таблицы рекрутеров (и чатбота) + Индикаторы рекрутеров
     * $hrs + $recruiters
     */
    public static function getTableRecruiters(array $users_ids, array $date) {
        $recruiters = [];
        $hrs = [];

        $startOfMonth = Carbon::createFromDate($date['year'], $date['month'], 1)->format('Y-m-d');

        $users_ids = array_unique($users_ids);
        foreach($users_ids as $user_id) {
         
            //  если есть записи у user, тогда берем их, 
            //  если нет берем пустой шаблон.
            $user = User::withTrashed()->find($user_id);
            if(!$user) continue;

            // Какие дни не учитывать в месяце
            $ignore = $user->working_day_id == 1 ? [6,0] : [0];

            $workdays = workdays($date['year'], $date['month'], $ignore);
            

            $xdate = Carbon::createFromDate($date['year'], $date['month'], 1)->format('Y-m-d');
            $wd = $user->workdays_from_applied($xdate, $user->working_day_id == 1 ? 5 : 6);

            if($wd != 0) {
                $workdays = $wd;
            }

            $asi = AnalyticsSettingsIndividually::where('group_id', self::GROUP_ID)
                ->where('employee_id', $user_id)
                ->whereYear('date', $date['year'])
                ->whereMonth('date', $date['month'])
                ->first();
            
            if($asi) {
                
                $data = json_decode($asi->data, true);

                foreach ($data as $index => $row) {
                    if($index == 0) $data[$index]['headers'] = 'Наборы с ожиданием 7 гудков';
                    if($index == 1) $data[$index]['headers'] = 'Успешные исходящие';
                    if($index == 2) $data[$index]['headers'] = 'Время первого звонка';
                    if($index == 3) $data[$index]['headers'] = 'Время последнего звонка';
                    if($index == 4) $data[$index]['headers'] = 'Обработано успешных входящих';
                    if($index == 5) $data[$index]['headers'] = 'Пропущенные звонки';
                    if($index == 6) $data[$index]['headers'] = 'Сконвертировано';
                    if($index == 7) $data[$index]['headers'] = 'Принято на работу';
                }

                array_push($hrs, [
                    'name' => $user->name . ' ' . $user->last_name,
                    'id' => $user->id,
                    'deleted' => false,
                    'workdays' => $workdays,
                    'records' => $data
                ]);

                $arr = self::getIndicators($user->id, $startOfMonth);

                if(count($arr) > 0 && !((int)$arr['out']['value'] == 0 && (int)$arr['converted']['value'] == 0)) {
                    array_push($recruiters, $arr);
                } 
                 
            } 
            
            
        }
        
        usort($recruiters, function($a, $b) {
            return $b['calls'] <=> $a['calls'];
        });

        array_push($hrs, self::getTableChatbot($date));

        return [
            'recruiters' => $recruiters,
            'hrs' => $hrs,
        ];
    }

    /**
     * Подробная таблица чатбота
     * @return array
     */
    public static function getTableChatbot(array $date) {
        $asi = AnalyticsSettingsIndividually::where('group_id', self::GROUP_ID)
                ->where('employee_id', 0)
                ->whereYear('date', $date['year'])
                ->whereMonth('date', $date['month'])
                ->first();
    
        return $asi ? [
            'name' => 'Чатбот',
            'id' => 0,
            'records' => json_decode($asi->data, true)
        ] : self::defaultChatbotTable();
    }

    /**
     * Заказы руководителей
     * @return array
     */
    public static function getOrders() {
        $orders = [];
        $orderGroups = ProfileGroup::where('active', 1)->get(); 
        foreach ($orderGroups as $group) {

            $applied = self::getApplied(date('Y-m-d'), $group->id);

            $orders[] = [
                'group' => $group->name,
                'required' => $group->required - $applied,
                'fact' => $group->provided . ' ',
            ];
        }

        return $orders;
    }

    /**
     * Count remain days
     * @return int
     */
    public static function daysRemain(array $date) {
        $start = Carbon::now();
        $end = Carbon::now()->setDate($date['year'], $date['month'], 1)->endOfMonth();
        
        $holidays = [
        //     Carbon::create(2014, 2, 2),
        ];

        $remain_days = 0;

        if($end->timestamp - $start->timestamp >= 0 && $end->month >= $date['month']) {
            $remain_days = $start->diffInDaysFiltered(function (Carbon $date) use ($holidays) {
                return !$date->isDayOfWeek(Carbon::SUNDAY); //&& !in_array($date, $holidays);
            }, $end);
        }

        return $remain_days;
    }


    /**
     * Причины увольнения
     * @return array
     */
    public static function fireCauses(array $date) {
        $causes = [];

        $uds = \DB::table('users')
            ->whereNotNull('deleted_at')
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where('ud.is_trainee', 0)
            ->where('users.UF_ADMIN', 1)
            ->whereYear('ud.fire_date', $date['year'])
            ->whereMonth('ud.fire_date', $date['month'])
            ->get()
            ->groupBy('fire_cause');
     
        foreach($uds as $key => $ud) {
            $causes[] = [
                'cause' => $key,
                'count' => $ud->count(),
            ];
        }
         
        $causes_keys = [];
        foreach ($causes as $key => $row)
        {
            $causes_keys[$key] = $row['count'];
        }

        array_multisort($causes_keys, SORT_DESC, $causes);

        return $causes;
    }

    /**
     * Таблица кадров в рекрутинг -> Причины увольнения
     * @return array
     */
    public static function staff($year) {

        $users_on = \DB::table('users')
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where('ud.is_trainee', 0)
            ->whereYear('applied', $year)
            ->get()
            ->groupBy(function($item) {
                return (int)Carbon::createFromFormat('Y-m-d H:i:s', $item->applied)->format('m');
            })->toArray();
        
        $users_off = \DB::table('users')
            ->whereNotNull('deleted_at')
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where('is_trainee', 0)
            ->whereYear('deleted_at', $year)
            ->get()
            ->groupBy(function($item) {
                return (int)Carbon::createFromFormat('Y-m-d  H:i:s', $item->deleted_at)->format('m');
            })->toArray();

        $staff = [];
        $staff[0]['name'] = 'Принято';
        $staff[1]['name'] = 'Уволено';
        $staff[2]['name'] = 'Баланс';
        $staff[3]['name'] = '% текучки';
        $staff[4]['name'] = 'Итого';

        for ($i=1; $i <=12; $i++) { 
            $staff[0]['m'.$i] = 0;
            if(array_key_exists($i, $users_on)) {
                foreach($users_on[$i] as $u) {
                    $staff[0]['m'.$i] += $u->full_time == 1 ? 1 : 0.5;
                }
            }
         
            $staff[1]['m'.$i] = 0;
            if(array_key_exists($i, $users_off)) {
                foreach($users_off[$i] as $u) {
                    $staff[1]['m'.$i] += $u->full_time == 1 ? 1 : 0.5;
                }
            }
            
            
            $staff[2]['m'.$i] =  $staff[0]['m'.$i] - $staff[1]['m'.$i];

            
            
            $staff[4]['m'.$i] =  self::getWorkerQuantity(Carbon::createFromDate($year, $i, 1));
            
            $staff[3]['m'.$i] = '0%';
            if($i != 1) {
                $a = $staff[4]['m'.($i - 1)] + $staff[0]['m'.$i];
                if($a > 0) {
                    $staff[3]['m'.$i] = round(( $staff[1]['m'.$i] / $a ) * 100, 1) . '%';
                } 
            }

        }

        return $staff;
    }

    /**
     * Таблица кадров в рекрутинг -> Продолжительность жизни сотрудников
     * @return array
     */
    public static function staff_longevity($year) {

        $staff = [];
        $staff[0]['name'] = 'менее 2 нед';
        $staff[1]['name'] = 'до 1 мес';
        $staff[2]['name'] = 'до 2 мес';
        $staff[3]['name'] = 'до 3 мес';
        $staff[4]['name'] = 'более 3 мес';
        $staff[5]['name'] = 'более 6 мес';
        $staff[6]['name'] = 'более 9 мес';
        $staff[7]['name'] = 'более 12 мес';

        for ($i=1; $i <=12; $i++) { 

            $users = \DB::table('users')
                ->whereNotNull('deleted_at')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('is_trainee', 0)
                ->whereMonth('deleted_at', $i)
                ->whereYear('deleted_at', $year)
                ->get(['users.id', 'users.deleted_at']);

            $staff[0]['m'.$i] = 0;
            $staff[1]['m'.$i] = 0;
            $staff[2]['m'.$i] = 0;
            $staff[3]['m'.$i] = 0;
            $staff[4]['m'.$i] = 0;
            $staff[5]['m'.$i] = 0;
            $staff[6]['m'.$i] = 0;
            $staff[7]['m'.$i] = 0;

            $total_fired = 0;
            foreach ($users as $key => $user) {

                $_user = User::withTrashed()->find($user->id);
                if(!$user) continue;
                $worked = $_user->wasPartOfTeam();

                // which cat to count user
                switch ($worked) {
                    case $worked >=0 && $worked <14:
                        $x = 0;
                        break;
                    case $worked >=14 && $worked <30:
                        $x = 1;
                        break;
                    case $worked >=30 && $worked <60:
                        $x = 2;
                        break;
                    case $worked >=60 && $worked <90:
                        $x = 3;
                        break;
                    case $worked >=90 && $worked <180:
                        $x = 4;
                        break;
                    case $worked >=180 && $worked <270:
                        $x = 5;
                        break;
                    case $worked >=270 && $worked <360:
                        $x = 6;
                        break;
                    case $worked >=360:
                        $x = 7;
                        break;
                    default:
                        $x = -1;
                }
                
                if($x >=0) {
                    $staff[$x]['m'.$i] += 1;
                    $total_fired++;
                }
                
            }

            for($j = 0;$j<=7;$j++) {
                if($total_fired > 0) {
                    $percent = floor($staff[$j]['m'.$i] / $total_fired * 100);
                    $staff[$j]['m'.$i] = $staff[$j]['m'.$i] . ' / ' . $percent . '%';
                } 
              
            }
           
           

        }

        return $staff;
    }

    /**
     * Таблица кадров в рекрутинг -> Причины увольнения
     * @return array
     */
    public static function staff_by_group($year) {

        $date = Carbon::createFromDate($year, 1, 1);
        $groups = ProfileGroup::where('active', 1)->get();

        $staffy = [];

        foreach ($groups as $key => $group) {
            $user_ids = ProfileGroup::employees($group->id, $date, 1) + ProfileGroup::employees($group->id, $date, 2);
            
            
           
            $staffy[$key]['name'] = $group->name;
            for ($i=1; $i <=12; $i++) { 
                
                $pgu = PGU::where('date', $date->month($i)->format('Y-m-d'))->where('group_id', $group->id)->first();

                $assigned = 0;
                $fired = 0;

                if($pgu) {
                    $assigneds = \DB::table('users')
                        ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                        ->whereIn('users.id', $pgu->assigned)
                        ->where('is_trainee', 0)
                        ->get();
                    foreach ($assigneds as $us) {
                        $assigned += $us->full_time == 1 ? 1 : 0.5;
                    }

                    $fireds = \DB::table('users')
                        ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                        ->whereIn('users.id', $pgu->fired)
                        ->where('is_trainee', 0)
                        ->get();

                    foreach ($fireds as $us) {
                        $fired += $us->full_time == 1 ? 1 : 0.5;
                    }
                }

                $worked = $assigned + $fired;
                
                $staffy[$key]['m'.$i] = '0%';
                if($worked > 0) {
                    $staffy[$key]['m'.$i] = round(( $fired / $worked ) * 100, 1) . '%';
                } 
                
            }

        }


        return $staffy;
    }

    /**
     * Текучка у группы в определенный месяц
     */

    
    public static function staff_on_group($date, $group_id) {

        $date = Carbon::parse($date)->day(1);
        $group = ProfileGroup::find($group_id);

        if(!$group) return 0;

        //$user_ids = ProfileGroup::employees($group->id, $date, 1) + ProfileGroup::employees($group->id, $date, 2);
       
        $pgu = PGU::where('date', $date->format('Y-m-d'))
            ->where('group_id', $group->id)
            ->first();

        $assigned = 0;
        $fired = 0;

        if($pgu) {
            $assigned = \DB::table('users')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->whereIn('users.id', $pgu->assigned)
                ->where('is_trainee', 0)
                ->get()
                ->count();
            // foreach ($assigneds as $us) {
            //     $assigned += $us->full_time == 1 ? 1 : 0.5;
            // }

            $fired = \DB::table('users')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->whereIn('users.id', $pgu->fired)
                ->where('is_trainee', 0)
                ->get()
                ->count();

            // foreach ($fireds as $us) {
            //     $fired += $us->full_time == 1 ? 1 : 0.5;
            // }
        }

        $worked = $assigned + $fired;
        
        $staff = 0;
        if($worked > 0) {
            $staff = round(( $fired / $worked ) * 100, 1);
        } 

        return $staff;
    }

    public static function getFiredInfo($date, $group_id) {

        $prev_date = Carbon::parse($date)->startOfMonth()->subMonth();
        $date = Carbon::parse($date)->startOfMonth();

        // $prev_employees = array_merge(ProfileGroup::employees($group_id, $prev_date, 1), ProfileGroup::employees($group_id, $prev_date, 2));
        // $employees = array_merge(ProfileGroup::employees($group_id, $date, 1), ProfileGroup::employees($group_id, $date, 2));
        

        $pgu_prev = PGU::where('group_id', $group_id)
            ->where('date', $prev_date->format('Y-m-d'))
            ->first();

        $pgu = PGU::where('group_id', $group_id)
            ->where('date', $date->format('Y-m-d'))
            ->first();
        
        $employees = [];
        $employees_fired = [];
        $prev_employees = [];
        $prev_employees_fired = [];
        if($pgu) {
            $employees = $pgu->assigned;
            $employees_fired = $pgu->fired;
        } 

        if($pgu_prev) {
            $prev_employees = $pgu_prev->assigned;
            $prev_employees_fired = $pgu_prev->fired;
        } 
        
        $users_off_prev = \DB::table('users')
            ->whereNotNull('deleted_at')
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where('is_trainee', 0)
            ->whereIn('users.id', $prev_employees)
            ->get();
        
        $users_off = \DB::table('users')
            ->whereNotNull('deleted_at')
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where('is_trainee', 0)
            ->whereIn('users.id', $employees_fired)
            ->get();

        $users_on = \DB::table('users')
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where('ud.is_trainee', 0)
            ->whereIn('users.id', $employees)
            ->get();
        
        $applied_users = \DB::table('users')
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where('is_trainee', 0)
            ->whereIn('users.id', array_merge($employees, $prev_employees))
            ->whereYear('ud.applied', $date->year)
            ->whereMonth('ud.applied', $date->month)
            ->get(); 

        $users_a = \DB::table('users')
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where('ud.is_trainee', 0)
            ->whereIn('users.id', $prev_employees)
            ->get();

        $working_prev =  0;
        foreach($users_a as $u) {
            $working_prev += $u->full_time == 1 ? 1 : 0.5;
        }

        $users_b = \DB::table('users')
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where('ud.is_trainee', 0)
            ->whereIn('users.id', $employees)
            ->get();

        $working =  0;
        foreach($users_b as $u) {
            $working += $u->full_time == 1 ? 1 : 0.5;
        }

        $fired_prev = 0;
        foreach($users_off_prev as $u) {
            $fired_prev += $u->full_time == 1 ? 1 : 0.5;
        }
         
        $fired = 0;
        foreach($users_off as $u) {
            $fired += $u->full_time == 1 ? 1 : 0.5;
        }
        
        $applied = 0;
        foreach($applied_users as $u) {
            $applied += $u->full_time == 1 ? 1 : 0.5;
        }
        
        
        $plus = $working_prev + $working;
        $percent = $plus > 0 ? $fired / ($working_prev + $working) : 0;

        $percent = round($percent * 100, 1);
         
        return [
            'fired' => $fired_prev,
            'percent' => $percent,
        ];
    }   
    
    /**
     * return number of applied workers
     */
    public static function getApplied($date, $group_id) {

        $date = Carbon::parse($date)->startOfMonth();

        $employees = array_merge(ProfileGroup::employees($group_id, $date, 1), ProfileGroup::employees($group_id, $date, 2));

        $users_off = \DB::table('users')
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where('is_trainee', 0)
            ->whereIn('users.id', $employees)
            ->whereYear('ud.applied', $date->year)
            ->whereMonth('ud.applied', $date->month)
            ->get()->count();

        return $users_off;
    }

    /**
     * return number of fired workers
     */
    public static function getFired($date, $group_id) {

        $date = Carbon::parse($date)->startOfMonth();

        $employees = array_merge(ProfileGroup::employees($group_id, $date, 1), ProfileGroup::employees($group_id, $date, 2));

        $users_off = \DB::table('users')
            ->whereNotNull('deleted_at')
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where('is_trainee', 0)
            ->whereIn('users.id', $employees)
            ->whereYear('deleted_at', $date->year)
            ->whereMonth('deleted_at', $date->month)
            ->get()->count();

        return $users_off;
    }

    public static function ocenka_svod($date) {
        $arr = [];

        $date = Carbon::parse($date)->startOfMonth();

        $groups1 = ProfileGroup::whereIn('id', [42])->get();
        $groups = ProfileGroup::where('active', 1)->where('has_analytics', 1)->get();
        $groups = $groups->merge($groups1);
        foreach($groups as $group) {
            $item = [];

            $item['name'] = $group->name;
            
            $leads = Lead::whereYear('invite_at', $date->year)
                ->whereMonth('invite_at', $date->month)
                ->where('invite_group_id', $group->id)
                ->get();

            $item['sent'] = $leads->count();

            $item['working'] = \DB::table('users')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('is_trainee', 0)
                ->whereIn('users.id', $leads->pluck('user_id')->toArray())
                ->get()
                ->count();
                
            $percent = $item['sent'] > 0 ? $item['working']/ $item['sent'] * 100 : 0;
            $item['percent'] = round($percent, 1);

            $item['active'] = DayType::where('date',Carbon::now()->toDateString())->whereIn('user_id', $leads->pluck('user_id')->toArray())->where('type',5)->get()->count();//$item['sent'];
            array_push($arr, $item);
        }

        return $arr;
    }
    
    public static function getPrognozGroups($date) {
        $arr = [];

        $groups = ProfileGroup::where('active', 1)->where('has_analytics', 1)->get();

        $old_groups = ProfileGroup::whereIn('id', [42])->get();

        $groups = $groups->merge($old_groups);
        foreach($groups as $group) {
            $item = [];

            
            $item['id'] = $group->id;
            $item['name'] = $group->name;
            $item['plan'] = $group->required;
            $working = ProfileGroup::employees($group->id, $date, 1, [32]);

            $rate = 0;

            foreach($working as $user_id) {
                $user = User::withTrashed()->find($user_id);
                if($user) {
                    $rate += $user->full_time == 1 ? 1 : 0.5;
                }
            }

            $item['applied'] = $rate;
            $item['left_to_apply'] = $group->required - $rate;
            array_push($arr, $item);
        }

        return $arr;
    }

    /**
     * Причины отсутствия 1 и 2 день стажировки
     * @return array
     */
    public static function getAbsenceCauses(array $date) {
        $first_day = [];
        $second_day = [];
        $third_day = [];

        // First day
        // $th1 = UserNotification::whereYear('created_at', $date['year'])
        //     ->whereMonth('created_at', $date['month'])
        //     ->where('title', 'Пропал с обучения: 1 день')
        //     ->whereNotNull('note')
        //     ->whereNotNull('read_at')
        //     ->get()
        //     ->groupBy('note');

        $th1 = UserAbsenceCause::whereYear('date', $date['year'])
            ->whereMonth('date', $date['month'])
            ->where('type', UserAbsenceCause::FIRST_DAY)
            ->get()
            ->groupBy('text');

        foreach($th1 as $key => $th) {
            $first_day[] = [
                'cause' => $key,
                'count' => $th->count(),
            ];
        }

        $DESC = array_column($first_day, 'count');
        array_multisort($DESC, SORT_DESC, $first_day);

        // Second day

        $th2 = UserAbsenceCause::whereYear('date', $date['year'])
            ->whereMonth('date', $date['month'])
            ->where('type', UserAbsenceCause::SECOND_DAY) 
            ->get()
            ->groupBy('text');

        foreach($th2 as $key => $th) {
            $second_day[] = [
                'cause' => $key,
                'count' => $th->count(),
            ];
        }

        $DESC = array_column($second_day, 'count');
        array_multisort($DESC, SORT_DESC, $second_day);

        // Third day

        $th2 = UserAbsenceCause::whereYear('date', $date['year'])
            ->whereMonth('date', $date['month'])
            ->where('type', UserAbsenceCause::THIRD_DAY) 
            ->get()
            ->groupBy('text');

        foreach($th2 as $key => $th) {
            $third_day[] = [
                'cause' => $key,
                'count' => $th->count(),
            ];
        }

        $DESC = array_column($third_day, 'count');
        array_multisort($DESC, SORT_DESC, $third_day);

        return [
            'first_day' => $first_day,
            'second_day' => $second_day,
            'third_day' => $third_day,
        ];
    }
    
    
    

    /**
     * Оценка операторов по группам 
     * @return array
     */
    public static function ratingsGroups(array $date) {
        $ratings = [];
        
        $rleads = Lead::whereYear('skyped', $date['year'])
            ->whereMonth('skyped', $date['month'])
            ->whereNotNull('invite_group_id')
            ->whereNotNull('rating')
            ->where('rating', '!=', 1)
            ->get()
            ->groupBy('invite_group_id');
        
        $rgroups = ProfileGroup::where('active', 1)->get();
        
        foreach ($rleads as $key => $rlead) {
            $rg = $rgroups->where('id', $key)->first();

            $rcount = 0;
            $rating_sum = 0;

            foreach ($rlead as $key => $lead) {
                $rcount++;
                $rating_sum += $lead->rating;
                if($lead->rating2) {
                    $rcount++;
                    $rating_sum += $lead->rating2;
                }
                
            }

            $ratings[] = [
                'group' => $rg ? $rg->name : $key,
                'count' => $rcount,
                'avg' => ($rating_sum / $rcount),
            ];
        }

        return $ratings;
    }


    /**
     * Оценка операторов даты
     * @return array
     */
    public static function ratingsDates(array $date) {
        $ratings_dates = [];
        
        $_date = Carbon::createFromFormat('m-Y', $date['month'] . '-' . $date['year']);
        $start = $_date->startOfMonth()->timestamp;
        $end = $_date->endOfMonth()->timestamp;

        $ratings_leads = UserDescription::where(function($query) {
			$query->whereNotNull('rating1')
				->orWhereNotNull('rating2');
        })->get();  
        
        foreach($ratings_leads as $rl) {
            $user = User::withTrashed()->find($rl->user_id);
            if($user) {
                $group = ProfileGroup::userIn($user->id, false);

                if($rl->rating2 && array_key_exists('date', $rl->rating2) && $rl->rating2['date'] > $start && $rl->rating2['date'] < $end
                ||
                $rl->rating1 && array_key_exists('date', $rl->rating1) && $rl->rating1['date'] > $start && $rl->rating1['date'] < $end) {
                    $ratings_dates[] = [
                        'name' => $user->last_name . ' ' . $user->name,
                        'rating' => $rl->rating1 && array_key_exists('rating', $rl->rating1) ?  $rl->rating1['rating'] : '-',
                        'rating_date' => $rl->rating1 && array_key_exists('date', $rl->rating1) ?  date('d.m.Y', $rl->rating1['date']) : '-',
                        'rating2' => $rl->rating2 && array_key_exists('rating', $rl->rating2) ?  $rl->rating2['rating'] : '-',
                        'rating2_date' => $rl->rating2 && array_key_exists('date', $rl->rating2) ?  date('d.m.Y', $rl->rating2['date']) : '-',
                        'group' => $group->count() > 0 ? $group[0]->name : '-',
                    ];
                }
                
            }
           
        }   

        return $ratings_dates;
    }



    /**
     * Count remain days
     * @return int
     */
    // public static function ratingsHeads(array $date) {
       
    // }
}   
