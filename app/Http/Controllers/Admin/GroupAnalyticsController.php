<?php

namespace App\Http\Controllers\Admin;

use DB;
use View;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Components\TelegramBot;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Http\Controllers\IntellectController as IC;
use App\Classes\Helpers\Phone;
use App\External\Bitrix\Bitrix;
use App\Classes\Analytics\Recruiting as RM;
use App\Classes\Analytics\Ozon;
use App\Classes\Analytics\Lerua;
use App\Classes\Analytics\DM;
use App\Classes\Analytics\HomeCredit;
use App\Classes\Analytics\Eurasian;
use App\Classes\Analytics\Tinkoff;
use App\User;
use App\Account;
use App\Trainee;
use App\UserDescription;
use App\UserNotification;
use App\Kpi;
use App\Zarplata;
use App\DayType;
use App\Salary;
use App\GroupSalary;
use App\CallBase;
use App\ProfileGroup;
use App\Timetracking;
use App\TimetrackingHistory;
use App\AnalyticsSettings;
use App\AnalyticsSettingsIndividually;
use App\Models\Analytics\Activity;
use App\Models\Analytics\ActivityTotal;
use App\Models\Analytics\ActivityPlan;
use App\Models\Bitrix\Lead;
use App\Models\Bitrix\Segment;
use App\QualityRecordMonthlyStat;
use App\Models\CallCenter\Directory;
use App\Models\CallCenter\Agent;
use App\Models\Analytics\RecruiterStat;
use App\Classes\Analytics\FunnelTable;
use App\Models\User\NotificationTemplate;
use App\Models\Analytics\DecompositionValue;
use App\Models\Analytics\DecompositionItem;
use App\Models\Analytics\TopValue;
use App\QualityRecordWeeklyStat;
use App\Models\Admin\Bonus;
use App\Models\Admin\ObtainedBonus;
use App\Models\Admin\EditedKpi;
use App\Models\Admin\EditedBonus;
use App\Models\Analytics\TraineeReport;
use App\ProfileGroupUser as PGU;

class GroupAnalyticsController extends Controller
{
    public $users;

    /**
     * Object with helper functions
     */
    private $helper;
    
    public function __construct()
    {
        View::share('title', 'Аналитика групп');
        $this->middleware('auth');
    }

    /**
     * Страница аналитика группы
     */
    public function index()
    {

        $superusers = User::where('is_admin', 1)->get(['id'])->pluck('id')->toArray();

        if(!in_array(Auth::user()->id, $superusers)) {

            $roles = \Auth::user()->roles ? \Auth::user()->roles : [];
            
            if(array_key_exists('page21', $roles) && $roles['page21'] == 'on') {}
            else {
                return redirect('timetracking/user/' . \Auth::user()->id);
            }
        }
        
        $currentUser = User::bitrixUser();

        $kaspi_42 = ProfileGroup::find(42);
        $recruting = ProfileGroup::find(48);
        // $dm = ProfileGroup::find(31);
       
        // $lerua = ProfileGroup::find(63);
        // $homecredit = ProfileGroup::find(57);
        // $eurasian = ProfileGroup::find(53);
        
        // Доступ к группе 

        $groups = [];
        $isSuperUser = in_array($currentUser->id, [5,18,157]);

        
        if ($recruting && in_array($currentUser->id, json_decode($recruting->editors_id)) || $isSuperUser) {
            $groups[] = [
                'id' => 48,
                'name' => $recruting->name,
                'groups' => [48],
            ];
        }  
        
        if ($kaspi_42 && in_array($currentUser->id, json_decode($kaspi_42->editors_id)) || $isSuperUser) {
            $groups[] = [
                'id' => 42,
                'name' => $kaspi_42->name,
                'groups' => [42],
            ];
        }

        
        
        // if ($dm && in_array($currentUser->id, json_decode($dm->editors_id)) || $isSuperUser) {
        //     $groups[] = [
        //         'id' => 31,
        //         'name' => $dm->name,
        //         'groups' => [31],
        //     ];
        // }   

     
        
        // if ($homecredit && in_array($currentUser->id, json_decode($homecredit->editors_id)) || $isSuperUser) {
        //     $groups[] = [
        //         'id' => 57,
        //         'name' => $homecredit->name,
        //         'groups' => [57],
        //     ];
        // }  
        
        // if ($eurasian && in_array($currentUser->id, json_decode($eurasian->editors_id)) || $isSuperUser) {
        //     $groups[] = [
        //         'id' => 53,
        //         'name' => $eurasian->name,
        //         'groups' => [53],
        //     ];
        // }   

        // if ($lerua && in_array($currentUser->id, json_decode($lerua->editors_id)) || $isSuperUser) {
        //     $groups[] = [
        //         'id' => 63,
        //         'name' => $lerua->name,
        //         'groups' => [63],
        //     ];
        // } 

      

        $groups = collect($groups);
        
        View::share('menu', 'timetracking_hr');
        return view('admin.analytics', compact('groups'));
    }

    /**
     * Пол©ить аналитику выбранной группы
     * AJAX на странице аналитика группы
     */
    public function getanalytics(Request $request)
    {
        $currentUser = User::bitrixUser();

        // Доступ к группе 
        $group = ProfileGroup::find($request['group_id']);
        if (!in_array($currentUser->id, json_decode($group->editors_id)) && $currentUser->id != 18) {
            return [
                'error' => 'access',
            ];
        }
        
        // if(in_array($request['group_id'], [35,42])) {
        //     return $this->kaspiAnalytics($request);
        // } else 
        if($request['group_id'] == RM::GROUP_ID) {
            return $this->recrutingAnalytics($request);
        } else {
            return $this->formAnalytics($request);
        }
    }
    
    /**
     * Сформировать сводную аналитику и подробную аналитику (Активности)
     * Стандартная функция
     */
    private function formAnalytics(Request $request) {
        $month = Carbon::createFromFormat('m-Y', $request->month . '-' . $request->year)->startOfMonth();
        $requestGroup = $group = ProfileGroup::find($request['group_id']);
        
        $settings = AnalyticsSettings::where('group_id', $request->group_id)
            ->where('type', 'basic') // Сводная
            ->whereYear('date', $request->year)
            ->whereMonth('date', $request->month)
            ->first();

        $users_ids = json_decode($group->users);
        //////
        $x_users = User::withTrashed()->where('UF_ADMIN', 1)
            ->whereDate('deleted_at', '>=', Carbon::createFromDate($request->year, $request->month, 1)->format('Y-m-d'))
            ->get(['id','last_group']);

        $fired_users = [];
        foreach($x_users as $d_user) {
            if($d_user->last_group) { 
                $lg = json_decode($d_user->last_group);
                if(in_array($request['group_id'], $lg)) {
                    array_push($fired_users, $d_user->id);
                }
            } 
        }
        
        
        /////
        if($settings && count($settings->users) > 0) {
            $users_ids = $settings->users;
        } 
        
        if($request->month == date('m') && $request->year == date('Y')) {
            $users_ids = json_decode($group->users);
        } 
         
        $users_ids = array_merge($users_ids, $fired_users);

        $users_ids = array_unique(array_values($users_ids)); 

        $this->users = User::withTrashed()
            ->where('UF_ADMIN', 1)
            ->whereIn('id', array_unique($users_ids))
            ->orderBy('full_name')
            ->get([
                'ID as id',
                'email as email',
                'name as name',
                'last_name as surname',
                DB::raw("CONCAT(last_name,' ',name) as full_name")
            ]);
    
        $result = [];
        $default_table = [];

        if($request->group_id == 31) { // DETSKI MIR
            $result['totals'] = DM::getTotals($month);
            $default_table =  DM::defaultSummaryTable();
        }

        
        if($request->group_id == 58 || $request->group_id == 59) { // OZON
            $result['totals'] = Ozon::getTotals($request->group_id, $month);
            $default_table =  Ozon::defaultSummaryTable();
        }

        if($request->group_id == 63) { // Леруа
            $result['totals'] = Lerua::getTotals($request->group_id, $month);
            $default_table =  Lerua::defaultSummaryTable();
        }

        if($request->group_id == 57) { // HOME CREDIT
            $result['totals'] = HomeCredit::getTotals($request->group_id, $month);
            $default_table =  HomeCredit::defaultSummaryTable();
        }

        if($request->group_id == 53) { // Евраз 1
            $result['totals'] = Eurasian::getTotals($request->group_id, $month);
            $default_table =  Eurasian::defaultSummaryTable();
        }

        if($request->group_id == 46) { // Тинькоф
            $result['totals'] = Tinkoff::getTotals($request->group_id, $month);
            $default_table =  Tinkoff::defaultSummaryTable();
        }


        if($request->group_id == 42) {
            $default_table = [];
            if($settings) {

                $individual_stats = AnalyticsSettingsIndividually::where(['date' => $month->startOfMonth(), 'group_id' => $request->group_id, 'type' => 1])->get();
                $data = $settings->data; 
    
                for ($i = 1; $i <= $month->daysInMonth; $i++) {
                    $data[4][$i] = 0;    
                }
    
                foreach($individual_stats as $individual_stat) {
                    $ind_data = json_decode($individual_stat->data, true);
                    if($ind_data) {
                        for ($i = 1; $i <= $month->daysInMonth; $i++) {
                            if(array_key_exists($i, $ind_data)) {
                                $data[4][$i] += $ind_data[$i];
                            } 
                        }  
                    }
                }

                $settings->data = $data; 
     
            }   
            
        }
        $result['decomposition'] = DecompositionValue::table($request->group_id, $month->startOfMonth()->format('Y-m-d'));
        $result['quality'] = QualityRecordWeeklyStat::table($users_ids, $month->format('Y-m-d'));

        $result['currentGroup'] = $request->group_id;
        $result['activities'] = $this->getActivities($request);
        $result['workdays'] = ProfileGroup::find($request->group_id)->workdays;

        $result['settings'] = is_null($settings) ? $default_table : $settings->data;

        $index = 2;
        if($request->group_id == 42 || $request->group_id == 31) {
            $index = 3;
        }
        if(array_key_exists($index,$result['settings']) && array_key_exists('plan', $result['settings'][$index])) {
            $gsalary = GroupSalary::where('group_id', $request->group_id)->where('date', $month->format('Y-m-d'))->get()->sum('total');
            $salary = floor($gsalary / 1000);
            $result['settings'][$index]['plan'] = $salary;
        }

        if($request->group_id == 53) {
            $result['call_bases'] = CallBase::formTable($month->format('Y-m-d'));
            $result['coef'] = CallBase::coefForEurasian($month->format('Y-m-d'));
        }

        if(in_array($request->group_id, [42,53,57,58,63])) {
            $util = TopValue::getUtilityGauges($month->format('Y-m-d'), [$request->group_id]);
            $rent = TopValue::getRentabilityGauges($month->format('Y-m-d'), [$request->group_id], 'Рентабельность');
            
            $util[0]['gauges'] = array_merge($util[0]['gauges'], $rent);
            
            $result['utility'] = $util;
        }
 
        return response()->json($result);
    }

    /**
     * 
     */
    public function saveCallBase(Request $request) {
        CallBase::saveTable([
            'total' => $request->total,
            'conversion' => $request->conversion,
            'current_credits' => $request->current_credits,
            'next_credits' => $request->next_credits,
            'current_given' => $request->current_given,
            'next_given' => $request->next_given,
        ],$request->date);
    }

    /**
     * Сформировать активности группы в определленный период времени 
     * Request $request 
     */
    public function getActivities(Request $request) {

       
        $activitiesWithData = [];

        $ids = Activity::where('group_id', $request->group_id)
            //->whereNotIn('id', [55,56,57,58,59,60,61,62,  67,68,69,70,71]) // OKK activities // rentability
            ->whereIn('type', ['default', 'collection'])
            ->get()
            ->pluck('id')
            ->toArray();
        //if($request->group_id == 31) $ids = [19,20,21]; //DM

        $activities = Activity::whereIn('id', $ids)->get();
     
        $date = Carbon::createFromDate($request->year, $request->month, 1)->format('Y-m-d');

         $group_1 = ProfileGroup::find($request->group_id);


         $ignore = $group_1->workdays == 5 ? [0,6] : [0];
            

            if($group_1->id == 53 && $request->year == 2022 && $request->month == 3) {
                $workdays = 19;
            } else if($group_1->id == 57  && $request->year == 2022 && $request->month == 3) {
                $workdays = 22;
            } else {
                $workdays = workdays($request->year, $request->month, $ignore);
            }

        foreach ($activities as $activity) {

            $records = [];

            foreach($this->users as $user) {
                
                $localUser = User::withTrashed()->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                    ->where('ud.is_trainee', 0)
                    ->where('UF_ADMIN', 1)
                    ->where('email', $user->email)
                    ->first();

                if(!$localUser) {
                    continue;
                } 
                
                /////////////////////

               
                $_user_ids = json_decode($group_1->users);

                $group_name = "";
                

                //////////////////////
                $check = AnalyticsSettingsIndividually::where([
                        'date' =>  $date, 
                        'type' => $activity->id,
                        'employee_id' => $localUser->id,
                        'group_id' => $request->group_id,
                    ])
                    ->where('group_id', $request->group_id)
                    ->first();
                
                if($localUser->deleted_at && $localUser->deleted_at != '0000-00-00 00:00:00') {
                    $fired = 1;
                } else {
                    $fired = 0;
                }

                $applied_from = $localUser->workdays_from_applied($date, $group_1->workdays);
                
                if($check) {
                    $_decoded = json_decode($check->data);
                    $_decoded->group = $group_name;
                    $_decoded->name = $localUser->name;
                    $_decoded->lastname = $localUser->last_name;
                    $_decoded->fullname = trim($localUser->last_name . ' ' . $localUser->name);
                    $_decoded->email = $localUser->email;
                    $_decoded->full_time = $localUser->full_time;
                    $_decoded->id = $localUser->id;
                    $_decoded->fired = $fired;
                    $_decoded->applied_from = $applied_from;
                    array_push($records, $_decoded);
                } else {
                    array_push($records, [
                        'name' => $localUser->name,
                        'lastname' => $localUser->last_name,
                        'fullname' => trim($localUser->last_name . ' ' . $localUser->name),
                        'email' => $localUser->email,
                        'group' => $group_name,
                        'full_time' => $localUser->full_time,
                        'id' => $localUser->id,
                        'fired' => $fired,
                        'applied_from' => $applied_from,
                    ]);
                }
            }
            
            



            /// get price for kaspi collection

            $price = 50;
            if($activity->group_id == 42) 
            {
                $bonuses = Bonus::where('activity_id', $activity->id)->first();
                if($bonuses) $price = $bonuses->sum;
            }
            

            // kaspi
            $table = 1;
            if(in_array($activity->id, [13,72,73,74,75])){
                $table = 2;
            }
            // 


            $arr = [
                'id' => $activity->id,
                'name' => $activity->name,
                'daily_plan' => $activity->daily_plan,
                'group_id' => $request->group_id,
                'plan_unit' => $activity->plan_unit,
                'workdays' => $workdays,
                'unit' => $activity->unit == '%' ? '%' : '',
                'table' => $table,
                'records' => $records,
                'price' => $price
            ];

            $activitiesWithData[] = $arr;
               
        }

        
        $data = [];

        if ($activitiesWithData) {
            $data = $activitiesWithData;
        } else {
            foreach($activities as $activity) {
                $data[] = [
                    'id' => $activity->id,
                    'name' => $activity->name,
                    'plan_unit' => $activity->plan_unit,
                    'records' => []
                ];
            }
        } 

        return json_encode($data);
    }


    /**
     * Получить ID нужных пользователей
     * Когда увольняют или исключают пользотвателя из группы, то сводная статистика без этих пользователей меняется. 
     * Для этого есть поле users в AnalyticsSettings
     * @param $args 
     *  int month
     *  int year
     *  int group_id
     *  array users
     * @return array
     */
    private function getUserIds(array $args) {
        $group = ProfileGroup::find($args['group_id']);

        $users_ids = [];

        if($group) {
            $users_ids = json_decode($group->users);

            if(!($args['month'] == date('m') && $args['year'] == date('Y')) && count($args['users']) > 0) {
                $users_ids = $args['users'];
            } 
        }
        
        /** Уволенные */
        $d_users = User::withTrashed()
            ->whereYear('deleted_at', $args['year'])
            ->whereMonth('deleted_at', $args['month'])
            ->where('last_group', '[' . $args['group_id'] . ']')
            ->get()
            ->pluck('id')
            ->toArray();

        $users_ids = array_merge($users_ids, $d_users);

        return $users_ids;
    }

    /**
     * Аналитика РЕКРУТИНГА
     * $request
     */
    private function recrutingAnalytics(Request $request) {
        $month = Carbon::createFromFormat('m-Y', $request->month . '-' . $request->year)->startOfMonth();
        $date = [
            'month' => $request->month,
            'year' => $request->year,
        ];

        $settings = RM::getSummaryTable($month);
        $data = $settings ? $settings->data : RM::defaultSummaryTable();
        
        $users_ids = $this->getUserIds([
            'group_id' => RM::GROUP_ID,
            'year' => $month->year,
            'month' => $month->month,
            'users' => $settings ? $settings->users : [],
        ]);

        $absence_causes = RM::getAbsenceCauses($date); // Причины отсутствия на 1 и 2 день стажировки
        $rec_tables = RM::getTableRecruiters($users_ids, $date);

        $hrs = $rec_tables['hrs']; // Для подробной таблицы

        $trainees = DayType::whereYear('date', $request->year) // Стажеры 
            ->whereMonth('date', $request->month)
            ->whereDay('date', RM::getLastDay($month))
            ->whereIn('type', [5,7])
            ->select(['user_id'])
            ->get()
            ->pluck('user_id')
            ->toArray();

        $trainees = count(array_unique($trainees));

        $recruiter_stats_rates = [];
        for ($i = 1; $i <= $month->daysInMonth; $i++) {
            $recruiter_stats_rates[$i] = $settings && array_key_exists($i, $data[RM::S_ONLINE]) ? $data[RM::S_ONLINE][$i] : 0;
        } 

        $remain_apply = $data[RM::S_APPLIED]['plan'] - $data[RM::S_APPLIED]['fact'];

        $indicators = []; 
        $indicators['info']['trainees'] = $trainees . ' - ' . round($trainees / $data[RM::S_CONVERTED]['fact'] * 100). '%'; // Cтажировались в этом месяце
        //$indicators['info']['training'] = $data[RM::S_TRAINING_TODAY]['plan']; // Cтажируются сегодня
        $indicators['info']['training'] = $trainees; // Cтажируются сегодня
        $indicators['info']['applied'] = $data[RM::S_APPLIED]['fact']. ' - ' . round($data[RM::S_APPLIED]['fact'] / $data[RM::S_CONVERTED]['fact'] * 100). '%'; // Принято сотрудников
        $indicators['info']['remain_apply'] = $remain_apply > 0 ? $remain_apply : 0; // Осталось аринять
        $indicators['info']['created'] = $data[RM::S_CREATED]['fact']; // Создано лидов
        $indicators['info']['processed'] = $data[RM::S_PROCESSED]['fact'] . ' - ' . round($data[RM::S_PROCESSED]['fact'] / $data[RM::S_CREATED]['fact'] * 100). '%'; // Обработано лидов
        $indicators['info']['converted'] = $data[RM::S_CONVERTED]['fact']. ' - ' . round($data[RM::S_CONVERTED]['fact'] / $data[RM::S_CREATED]['fact'] * 100). '%' ; // Сконвертировано лидов
        $indicators['info']['fired'] = $data[RM::S_FIRED]['fact'] ;  // Увоолено в этом месяце
        $indicators['info']['applied_plan'] = $data[RM::S_APPLIED]['plan'];// План по принятию на штат на месяц
        $indicators['info']['remain_days'] = RM::daysRemain($date); // Осталось рабочих дней до конца месяца
        $indicators['info']['working'] = $settings && array_key_exists('working', $settings->extra) ? $settings->extra['working'] : RM::getWorkerQuantity(); // Кол-во работающих (Ставка)
        $indicators['recruiters'] = $rec_tables['recruiters']; // Для графической аналитики
        $indicators['orders'] = RM::getOrders(); // Заказы стажеров от руководителей 
        $indicators['today'] = date('d');
        $indicators['month'] = $request->month;

        return [
            'date' => $month->startOfMonth()->format('Y-m-d'),
            'records' => $data, // Сводная таблица
            'hrs' => $hrs, // Подробные таблицы рекрутеров
            'skypes' => Lead::fetch($date), // Cконвертированные сделки. Раньше собирали скайпы (Нужно переименовать)
            'segments' => Segment::pluck('name', 'id'), // Cегменты
            'indicators' => $indicators, // Разные показатели на главной
            'sgroups' => ProfileGroup::where('active', 1)->get(), // Группы для приглашения
            'invite_groups' => ProfileGroup::pluckIdName(), // Фильтр для таблицы "стажеры"
            'causes' => RM::fireCauses($date), // причины увольнения
            'absents_first' => $absence_causes['first_day'], 
            'absents_second' => $absence_causes['second_day'],
            'absents_third' => $absence_causes['third_day'],
           // 'ratings' => RM::ratingsGroups($date), // Оценки операторов по группам
            'staff' => RM::staff($request->year), // Таблица кадров во вкладке причина увольнения
            'staff_by_group' => RM::staff_by_group($request->year), // Таблица кадров во вкладке причина увольнения
            'staff_longevity' => RM::staff_longevity($request->year), // Таблица кадров во вкладке причина увольнения
            'quiz' => RM::getQuizTable($month->startOfMonth()), // Анкета уволенных
            'ocenka_svod' => RM::ocenka_svod($month->startOfMonth()), // Анкета уволенных
           // 'ratings_dates' => RM::ratingsDates($date), // Оценки операторов по датам
            //'ratings_heads' => UserDescription::getHeadsRatings($month->startOfMonth()), // Оценки операторов по руководителям
            'recruiter_stats' => RecruiterStat::tables($month->startOfMonth()->format('Y-m-d')), // Почасовая таблица на главной
            'recruiter_stats_rates' => $recruiter_stats_rates, // Кол-во рекрутеров (Ставка)
            'recruiter_stats_leads' => RecruiterStat::leads($month->startOfMonth()->format('Y-m-d')), // Кол-во лидов битрикс в статусе "В работе"
            'funnels' => FunnelTable::getTables($month->startOfMonth()->format('Y-m-d')), // Воронки
            'decomposition' => DecompositionValue::table($request->group_id, $month->format('Y-m-d')),
            'trainee_report' => TraineeReport::getBlocks($month->format('Y-m-d')), // оценки первого дня и присутствие стажеров
            'workdays' => ProfileGroup::find(48)->workdays
        ];
    }

    /**
     * Перенаправление на битрикс сделку по lead_id
     * @int $id - lead_id в битриксе
     * $x, $y - артефакты роутера (НУЖНО УБРАТЬ)
     */
    public function redirectToBitrixDeal($id) {
        $lead = Lead::where('lead_id', $id)->first();
   
        $deal_id = 0;

        if($lead) {

            if($lead->deal_id == 0) {
                $bitrix = new Bitrix();
                $deal_id = $bitrix->findDeal($lead->lead_id, false);
            } else {
                $deal_id = $lead->deal_id;
            }
            
        }

        if($deal_id == 0) {
            return redirect('https://infinitys.bitrix24.kz/crm/lead/details/' . $id . '/');
        } else {
            return redirect('https://infinitys.bitrix24.kz/crm/deal/details/' . $deal_id . '/');
        }

        
    }

    /**
     * Пригласить на стажировку во вкладке Аналитика Групп (Рекрутинг) - Стажеры
     * Создает пользователей и меняет сделку в битриксе
     */
    public function inviteUsers(Request $request) {

        $leads = Lead::whereIn('id', $request->users)->get();
        $whatsapp = new IC();
        
        /////////// check group and zoom link existence

        $group = ProfileGroup::find($request['group_id']);

        if(!$group) {
            return [
                'code' => 201
            ];
        }



        // save users migrations

        $pgu = PGU::where('group_id', $group->id)
            ->where('date', Carbon::now()->day(1)->format('Y-m-d'))
            ->first();  

        if($pgu) {
            $arr = array_unique(array_merge($pgu->assigned, $request->users));
            $arr = array_values($arr);
            $pgu->assigned = $arr;
            $pgu->save();
        }
        ////
    
        if($request->time) {
            $hour = substr($request->time, 0, 2);
            $minute = substr($request->time, 3, 2);
            $invite_at = Carbon::parse($request->date)->hour($hour)->minute($minute); 
        } else {
            $invite_at = Carbon::parse($request->date); 
        }

        $day_second = Carbon::parse($request->date)->addDays(1); 
        if($day_second->dayOfWeek == 6) $day_second->addDays(2);
        if($day_second->dayOfWeek == 0) $day_second->addDays(1);

        $msg_for_group_leader = '';

        $has_remote_to_send_notification = false;
        //////////
        foreach ($leads as $lead) {
           
            // Проверить существует ли user


            $original_password = User::generateRandomString();
            $salt = User::randString(8);
            $user_password = $salt . md5($salt . $original_password);


            if($lead->email) {
                $email = $lead->email;
            } else {
                $email = 'user' . $lead->lead_id . '@bpartners.kz';
                if($lead->status == 'MAN') {
                    $email = 'person' . $lead->id . '@bpartners.kz';
                }
            }

            try {
                if(in_array($lead->wishtime, [4,5,6])) {
                    $full_time = 0;
                } else {
                    $full_time = 1;
                }
            } catch(\Exception $e) {
                $full_time = 1;
            }
            
            $user = User::withTrashed()->where('email', $email)->first();

            $currency = Phone::getCurrency($lead->phone);
            $user_type = $lead->skyped ? 'remote' : 'office';

            if($user_type == 'remote') {
                $has_remote_to_send_notification = true;
            }

            $uname = strlen($lead->name) > 50 ? mb_substr($lead->name, 0, 49) : $lead->name;
            if(!$user) {
                
                 
                $user = User::create([
                    'email' => $email,
                    'name' => $uname,
                    'last_name' => '',
                    'description' => $email,
                    'password' => \Hash::make('12345'),
                    'position_id' => 32, // Оператор
                    'user_type' => $user_type,
                    'timezone' => 6,
                    'bitrhday' => now(),
                    'program_id' => 2,
                    'working_day_id' => 2,
                    'working_time_id' => 2,
                    'full_time' => $full_time,
                    'phone' => $lead->phone,
                    'work_start' => null,
                    'work_end' => null,
                    'segment' => $lead->segment,
                    'currency' => $currency,
                    'role_id' => 1,
                    'is_admin' => 0 
                ]); 
                
                UserDescription::make([
                    'user_id' => $user->id,
                    'lead_id' => $lead->lead_id,
                    'deal_id' => $lead->deal_id,
                    'is_trainee' => 1,
                ]);

                // create trainee
                $trainee = Trainee::where('user_id', $user->id)->first();
                if(!$trainee) {
                    $trainee = Trainee::create([
                        'user_id' => $user->id,
                        'lead_id' => $lead->lead_id,
                        'deal_id' => $lead->deal_id,
                    ]);  
                } else {
                    $trainee->lead_id = $lead->lead_id;
                    $trainee->deal_id = $lead->deal_id;
                    $trainee->save();
                }

                    
                $old_invite_at = $lead->invite_at;
                $lead->invite_at = $invite_at;
                $lead->day_second = $day_second;
                $lead->user_id = $user->id;
                $lead->invite_group_id = $request->group_id;    
                $lead->invited = 1;

                if($user_type == 'remote') {
                    $msg_for_group_leader = $this->msgForGroupLeader($msg_for_group_leader, $user);
                }

                
                
            } else {

                
                if($user->UF_ADMIN == 1) {

                    $old_invite_at = $lead->invite_at;
                    $lead->invite_at = $invite_at;
                    $lead->day_second = $day_second;
                    $lead->user_id = $user->id;
                    $lead->invite_group_id = $request->group_id;    
                    $lead->invited = 2; // Сотрудник уже существует
                    
                    if($user_type == 'remote') {
                        $msg_for_group_leader = $this->msgForGroupLeader($msg_for_group_leader, $user);
                    }
              
                    
                    UserDescription::make([
                        'user_id' => $user->id,
                        'lead_id' => $lead->lead_id,
                        'deal_id' => $lead->deal_id,
                        'is_trainee' => 1,
                    ]);

                    // create trainee
                    $trainee = Trainee::where('user_id', $user->id)->first();
                    if(!$trainee) {
                        $trainee = Trainee::create([
                            'user_id' => $user->id,
                            'lead_id' => $lead->lead_id,
                            'deal_id' => $lead->deal_id,
                        ]);  
                    } else {
                        $trainee->lead_id = $lead->lead_id;
                        $trainee->deal_id = $lead->deal_id;
                        $trainee->save();
                    }

                    $user->segment = $lead->segment;
                    $user->save();
                    //
                } else {
                    $user->update([
                        'description' => $lead->name,
                        'phone' => $lead->phone, // ????????????
                        'position_id' => 32, // Оператор
                        'program_id' => 2,
                        'full_time' => $full_time,
                        'working_day_id' => 2,
                        'working_time_id' => 2,
                        'currency' => $currency,
                        'segment' => $lead->segment,
                        'user_type' => $user_type,
                    ]);
                    
                    UserDescription::make([
                        'user_id' => $user->id,
                        'lead_id' => $lead->lead_id,
                        'deal_id' => $lead->deal_id,
                        'is_trainee' => 1,
                    ]);

                    // create trainee
                    $trainee = Trainee::where('user_id', $user->id)->first();
                    if(!$trainee) {
                        $trainee = Trainee::create([
                            'user_id' => $user->id,
                            'lead_id' => $lead->lead_id,
                            'deal_id' => $lead->deal_id,
                        ]);  
                    } else {
                        $trainee->lead_id = $lead->lead_id;
                        $trainee->deal_id = $lead->deal_id;
                        $trainee->save();
                    }

                    $old_invite_at = $lead->invite_at;
                    $lead->invite_at = $invite_at;
                    $lead->day_second = $day_second;
                    $lead->user_id = $user->id;
                    $lead->invite_group_id = $request->group_id;    
                    $lead->invited = 1;

                    if($user_type == 'remote') {
                        $msg_for_group_leader = $this->msgForGroupLeader($msg_for_group_leader, $user);
                    }
                    
                }
            }

            $lead->save();

            
            /*==============================================================*/
            /*******  Создание пользователя в Callibro.org */
            /*==============================================================*/

            $account = Account::where('email', $email)->first();
            if (!$account) {

                if($lead->name == '') {
                    $lead->name = 'Без имени';
                }
                $account = Account::create([
                    'password' => User::randString(16),
                    'owner_uid' => 5,
                    'name' => $uname,
                    'surname' => '',
                    'email' => strtolower($email),
                    'status' => Account::ACTIVE_STATUS,
                    'role' => [Account::OPERATOR],
                    'activate_key' => '',
                ]);
            }

            /** zarplata */
            $zarplata = Zarplata::where('user_id', $user->id)->first();
            if($zarplata) {
                $zarplata->zarplata = 70000;
                $zarplata->save();
            } else {
                Zarplata::create([
                    'zarplata' => 70000,
                    'user_id' => $user->id,
                    'card_number' => '',
                    'kaspi' => '',
                    'jysan' => '',
                    'card_kaspi' => '',
                    'card_jysan' => '',
                ]);
            }

            /*==============================================================*/
            /*******  Зачисление пользователя в группу */
            /*==============================================================*/
            
            // Удаление стажера из всех груп
            $groups = $user->inGroups();
    
            foreach($groups as $gr) {
                $gr_users = json_decode($gr->users);
                $gr_users = array_diff($gr_users, [$user->id]);
                $gr_users = array_values($gr_users);

                $gr->users = json_encode($gr_users);
                unset($gr->show);
                $gr->save();
            }

            // Зачисление в выбранную группу
            if ($group->users !== null) {

                $users_array = json_decode($group->users);
                $users_array[] = $user->id;
            } else {
                $users_array = [];
                $users_array[] = $user->id;
            }
            $group->users = json_encode($users_array);
            $group->save();
            
            /*==============================================================*/
            /*******  Начало стажировки */
            /*==============================================================*/

            $date = $request->date;

            if($old_invite_at) {
                $daytype = DayType::where([
                    'user_id' => $user->id,
                    'type' => DayType::DAY_TYPES['TRAINEE'],
                    'date' => Carbon::parse($old_invite_at)->format('Y-m-d'), 
                ])->first();
    
                if($daytype) $daytype->delete();
            } 

            $daytype = DayType::where([
                'user_id' => $user->id,
                'date' => $date, 
            ])->first();

            if($daytype) {
                $daytype->admin_id = 1;
                $daytype->type = DayType::DAY_TYPES['TRAINEE'];
                $daytype->save();
            } else {
                $daytype = DayType::create([
                    'user_id' => $user->id,
                    'type' => DayType::DAY_TYPES['TRAINEE'],
                    'email' =>'',
                    'date' => $date,
                    'admin_id' => 1,
                ]);
            }
           
        }
        
        /*==============================================================*/
        /*******  Уведомление руководителю группы */
        /*==============================================================*/

        $msg_for_group_leader .= 'Приглашение в группу "' . $group->name . '" на ' . date('d.m.Y', strtotime($request->date)) . ' в 9:30';
        
        $heads = json_decode($group->head_id); 

        $timestampx = now();

        $notification_receivers = NotificationTemplate::getReceivers(6, $group->id);
        
        if($has_remote_to_send_notification) {
            foreach($notification_receivers as $user_id) {
                UserNotification::create([
                    'user_id' => $user_id,
                    'about_id' => 0,
                    'title' => 'Добавьте в Ватсап',
                    'message' => $msg_for_group_leader,
                    'group' => $timestampx
                ]);
            }
        }
        
        
        return [
            'code' => 200
        ];
    }

    /**
     * Сформировать сообщение для руководителя на ватсап
     */
    private function msgForGroupLeader(String $msg, User $user) {
        $msg .= $user->phone;
        if($user->phone_1) $msg .= ', ' . $user->phone_1;
        if($user->phone_2) $msg .= ', ' . $user->phone_2;
        $msg .= ' : ' . $user->last_name . ' ' . $user->name . '<br>';
        return $msg;
    }

    /**
     * Аналитика КАСПИ 
     * Группы 
     * 35 Напоминание
     * 42 Просрочка
     * 56 Сейчас нет
     */
    private function kaspiAnalytics(Request $request) {
        $month = Carbon::createFromFormat('m-Y', $request->month . '-' . $request->year);
        $requestGroup = $group = ProfileGroup::find($request['group_id']);

        $settings = AnalyticsSettings::whereYear('date', $request->year)
            ->whereMonth('date', $request->month)
            ->where('group_id', $request->group_id)
            ->where('type', 'basic')
            ->first();

        $users_ids = json_decode($group->users);

        if($settings && count($settings->users) > 0) {
            $users_ids = $settings->users;
        } 
        
        if($request->month == date('m') && $request->year == date('Y')) {
            $users_ids = json_decode($group->users);
        } 
        
        $result = array_unique($users_ids);
        $users_emails = User::find(array_unique($result))->pluck('email');


        if (isset($users_ids)) {

            $timetrackings = Timetracking::selectRaw("*,DATE_FORMAT(enter, '%e') as date, TIMESTAMPDIFF(hour, `enter`, `exit`) as hour")
                ->whereYear('enter', '=', date('Y'))
                ->whereMonth('enter', '=', $request->month)
                ->whereIn('user_id', $users_ids)->get();
            
            $group_accounts = User::withTrashed()
                ->where('UF_ADMIN', 1)
                ->whereIn('email', $users_emails)
                ->orderBy('full_name')
                ->get(['ID as id', 'email as email', 'name as name', 'last_name as surname', DB::raw("CONCAT(last_name,' ',name) as full_name")]);
            
            $this->users = $group_accounts;

            $account_ids = array_unique($group_accounts->pluck('id')->toArray());

            // users_other_emails

            $callsPerDay = [];
            $callsStagerPerDay = [];
            $callsPerOtherDay = [];
            $consentPerDay = [];
            $consentStagerPerDay = [];
            $callMinutesPerDay = [];
            $callStagerMinutesPerDay = [];
            $notCorrectCalls = [];
            $closedCards = [];
            $lostCalls = [];

        }


        $workHourDay = [];
        $workHourDayOtherGroup = [];

        if(!(in_array($request->month, [9,10]) && date('Y') == 2021)) {
            if($settings) {

                $individual_stats = AnalyticsSettingsIndividually::where(['date' => $month->startOfMonth(), 'group_id' => $request->group_id, 'type' => 1])->get();
                $data = $settings->data; 
    
                for ($i = 1; $i <= $month->daysInMonth; $i++) {
                    if($request['group_id'] == 35) {
                        $data[5][$i] = 0;
                    } else {
                        $data[4][$i] = 0;    
                    }
                }
    
                foreach($individual_stats as $individual_stat) {
                    
                    
                    $ind_data = json_decode($individual_stat->data, true);
            
                    if($ind_data) {
                        for ($i = 1; $i <= $month->daysInMonth; $i++) {
                            
                            if(array_key_exists($i, $ind_data)) {
                                if($request->group_id == 35) {
                                    $data[5][$i] += $ind_data[$i]; // нап
                                } else {
                                    $data[4][$i] += $ind_data[$i];
                                }
                            } 
                            
                        }  
                    }
                    
                }

                $settings->data = $data; 
    
            }   
        }
        
        
        
        for ($i = 1; $i <= $month->daysInMonth; $i++) {
            if ($sum = $timetrackings->where('date', $i)->sum('hour')) {
                if (!isset($workHourDay[$i])) {
                    $workHourDay[$i] = 0;
                }
                $workHourDay[$i] += $sum;
            }
        }
        
        $result = [
            'currentGroup' => $request->group_id,
            'accounts' => isset($group_accounts) ? $group_accounts->toArray() : [],
            'plan' => isset($requestGroup->plan) ? $requestGroup->plan->toArray() : [],
            'calls' => $callsPerDay,
            'callsOther' => $callsPerOtherDay,
            'consentPerDay' => $consentPerDay,
            'consentStagerPerDay' => $consentStagerPerDay,
            'callMinutesPerDay' => $callMinutesPerDay,
            'callStagerMinutesPerDay' => $callStagerMinutesPerDay,
            'notCorrectCalls' => $notCorrectCalls,
            'closedCards' => $closedCards,
            'lostCalls' => $lostCalls,
            'activities' => $this->getActivities($request),
            'workHourDay' => $workHourDay,
            'workHourDayOtherGroup' => $workHourDayOtherGroup,
            'settings' => is_null($settings) ? null : $settings->data,
            'decomposition' => DecompositionValue::table($request->group_id, $month->format('Y-m-d')),
            'utility' => TopValue::getUtilityGauges($month->format('Y-m-d'), [42]),
            'workdays' => ProfileGroup::find($request->group_id)->workdays,
            'quality' => QualityRecordWeeklyStat::table($users_ids, $month->format('Y-m-d'))
        ];
        
        return response()->json($result);
    }

    /**
     * ЗАЧЕМ НУЖНА ЭТА ФУНКЦИЯ?????
     */
    public function updateExtra(Request $request) { // Нужно потом объеденить с update()
        $currentUser = User::bitrixUser();
        $settings = AnalyticsSettings::where('date', $request->date)
            ->where('group_id', $request->group_id)
            ->where('type', $request->type)
            ->first();   

        $data = array_filter($request->settings);

        if (!is_null($settings)) {
            $settings->data = $data;
            $settings->save();
            return;
        }

        $settings = AnalyticsSettings::create([
            'group_id' => $request->group_id,
            'date' => $request->date,
            'type' => $request->type,
            'data' => $data,
            'user_id' => $currentUser->id,
        ]);
    }

    /**
     * Сохранить изменения в сводной таблице
     */
    public function update(Request $request)
    {
        $currentUser = User::bitrixUser();
        $settings = AnalyticsSettings::where('date', $request->date)
            ->where('group_id', $request->group_id)
            ->where('type', 'basic')
            ->first();


        if($request->group_id == 42 && $request->add_hours) {
            $date = Carbon::parse($request->date)->day($request->add_hours['day'])->format('Y-m-d');
            $group_users = json_decode(ProfileGroup::find(42)->users);
            $tts = Timetracking::whereIn('user_id', $group_users)
                ->whereDate('enter', $date)
                ->orderBy('enter', 'desc')
                ->get();

            
            $marked_users = [];

            $value = (int)$request->add_hours['value'];
            $day = $request->add_hours['day'];
            $user_type = $request->add_hours['user_type'];

            $old_value = 0;

            if($user_type == 'remote') {
                $field_index = 22;
            } else {
                $field_index = 23;
            }
            
            if($settings && array_key_exists($field_index, $settings->data) && array_key_exists($day, $settings->data[$field_index])) {
                $old_value = (int)$settings->data[$field_index][$day];
            }

            foreach($tts as $tt) {
                $user = User::find($tt->user_id);
                if(!$user)continue;
                if(!$user->user_type)continue;
                if($user->user_type != $user_type) continue;

                if(!in_array($tt->user_id, $marked_users)) {

                    $new_value = $tt->total_hours + $value - $old_value;
                    if($new_value < 0) $new_value = 0;
                    $tt->total_hours = $new_value; 

                    $tt->updated =  1;
                    $tt->save();
                    
                    array_push($marked_users, $tt->user_id);

                    if($value == 0) {
                        $desc = 'Отмена: Минуты за "Отсутствие связи"';
                    } else {
                        $old_text = $old_value != 0 ? ', минус предыдущие добавленные ' . $old_value . ' минут' : '';
                        $desc = 'Отсутствие связи. <br> Добавлено '. $value . ' минут ' . $old_text;
                    }

                    TimetrackingHistory::create([
                        'user_id' => $tt->user_id,
                        'author_id' => Auth::user()->id,
                        'author' => Auth::user()->last_name . ' ' . Auth::user()->name,
                        'date' => $date,
                        'description' => $desc,
                    ]);
                        
                    
                }
            }
        } 

        if($request->group_id == 57 && $request->add_hours) {
            $date = Carbon::parse($request->date)->day($request->add_hours['day'])->format('Y-m-d');
            $group_users = json_decode(ProfileGroup::find(57)->users);
            $tts = Timetracking::whereIn('user_id', $group_users)
                ->whereDate('enter', $date)
                ->where('program_id', 1) // ucalls
                ->orderBy('enter', 'desc')
                ->get();

            
            $marked_users = [];

            $value = (int)$request->add_hours['value'];
            $day = $request->add_hours['day'];
            $user_type = $request->add_hours['user_type'];

            $old_value = 0;

            if($user_type == 'remote') {
                $field_index = 12;
            } else {
                $field_index = 11;
            }
            
            if($settings && array_key_exists($field_index, $settings->data) && array_key_exists($day, $settings->data[$field_index])) {
                $old_value = (int)$settings->data[$field_index][$day];
            }

            foreach($tts as $tt) {
                $user = User::find($tt->user_id);
                if(!$user)continue;
                if(!$user->user_type)continue;
                if($user->user_type != $user_type) continue;

                if(!in_array($tt->user_id, $marked_users)) {

                    $new_value = $tt->total_hours + $value - $old_value;
                    if($new_value < 0) $new_value = 0;
                    $tt->total_hours = $new_value; 

                    $tt->updated =  3;
                    $tt->save();
                    
                    array_push($marked_users, $tt->user_id);

                    if($value == 0) {
                        $desc = 'Отмена: Минуты за "Отсутствие связи"';
                    } else {
                        $old_text = $old_value != 0 ? ', минус предыдущие добавленные ' . $old_value . ' минут' : '';
                        $desc = 'Отсутствие связи. <br> Добавлено '. $value . ' минут ' . $old_text;
                    }

                    TimetrackingHistory::create([
                        'user_id' => $tt->user_id,
                        'author_id' => Auth::user()->id,
                        'author' => Auth::user()->last_name . ' ' . Auth::user()->name,
                        'date' => $date,
                        'description' => $desc,
                    ]);
                        
                    
                }
            }
        }

        $data = array_filter($request->settings);

        if (!is_null($settings)) {
            $settings->data = $data;
            $settings->save();
            return;
        }

        $settings = AnalyticsSettings::create([
            'group_id' => $request->group_id,
            'date' => $request->date,
            'type' => 'basic',
            'data' => $data,
            'user_id' => $currentUser->id,
        ]);
    }

    /**
     * Сохранить изменения Активности сотрудника (Таблицы во вкладке "Подробная")
     * Хранит ряд в таблице
     */
    public function updateIndividually(Request $request)
    {   
        $currentUser = User::bitrixUser();
        $employee_id = $request->employee_id;
        $group_id = $request->group_id;
        $type = $request->table_type;
        $date = $request->date;
        $data = json_encode($request->settings);
     

        // CHECK existing record
        if($group_id == 48) {
            $setting = AnalyticsSettingsIndividually::where(['date' => $date, 'group_id' => $group_id, 'employee_id' => $employee_id])->first();
        } else {
            $setting = AnalyticsSettingsIndividually::where(['date' => $date, 'group_id' => $group_id, 'type' => $type, 'employee_id' => $employee_id])->first();
        }

        if ($setting) {
            $setting->data = $data; 
            $setting->user_id = $currentUser->id;
            $setting->employee_id = $employee_id;
            $setting->save();
        } else {
            $setting = AnalyticsSettingsIndividually::create([
                'group_id' => $request->group_id,
                'date' => $date,
                'employee_id' => $employee_id,
                'type' => $type,
                'data' => $data,
                'user_id' => $currentUser->id,
            ]);
        }

        if($group_id == DM::GROUP_ID && $type == DM::ACTIVITIES[1]) {
            DM::updateTimes($employee_id, $date, $request->day);
        }

        if($group_id == DM::GROUP_ID && $type == DM::ACTIVITIES[2] && array_key_exists($request->day, $request->settings)) {
            DM::updateTimesByWorkHours($employee_id, $date, $request->day, $request->settings[$request->day]);
        }

        
        if(in_array($group_id, Ozon::GROUP_ID) && $type == Ozon::ACTIVITIES[$group_id][0]) {
            Ozon::updateTimes($group_id, $employee_id, $date, $request->day);
        }

        if($group_id == 48 && $request->day) {
            \Artisan::call('bonus:update', [
                'date' => Carbon::parse($date)->day((int)$request->day)->format('Y-m-d'),
                'group_id' => 48,
            ]);
        }
        
        if(in_array($group_id, Lerua::GROUP_ID) && $type == Lerua::ACTIVITIES[$group_id][0]) {
            Lerua::updateTimes($group_id, $employee_id, $date, $request->day);
        }

        
        
    }

    /**
     * Создать лид стажера вручную
     */
    public function createRecrutingLead(Request $request) {
        return Lead::create([
            'lead_id' => 0,
            'deal_id' => 0,
            'name' => $request->name,
            'phone' => Phone::normalize($request->phone),
            'wishtime' => $request->wishtime,
            'phone_2' => null,
            'phone_3' => null,
            'status' => 'MAN',
            'hash' => 'created_manually',
            'skyped' => now(),
            'lang' => $request->lang,
        ]);
    }

    /**
     * Сформировать активности для группы KASPI
     */
    public function getActivitiesWithData(Request $request, $working = 'all') {

        //$activities = DB::table('activities')->where('group_id', $request->group_id)->get();
        //$activities = DB::table('activities')->whereIn('id', [1,5,13])->get();
        
        $activitiesWithData = [];

        if($request->group_id == 35) $activities = DB::table('activities')->whereIn('id', [1])->get();
        if($request->group_id == 42) $activities = DB::table('activities')->where('group_id', 42)->get();
       // if($request->group_id == 56) $activities = DB::table('activities')->whereIn('id', [1,15])->get();
        
        $date = Carbon::createFromDate($request->year, $request->month, 1)->format('Y-m-d');

        foreach ($activities as $activity) {

            $data  = AnalyticsSettingsIndividually::where([
                    'date' =>  $date, 
                    'type' => $activity->id,
                ])
                //->whereIn('group_id', [35,42])
                ->where('group_id', $request->group_id)
                ->get();
            
            $records = [];

            foreach($this->users as $user) {
                
                $localUser = User::withTrashed()
                        ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                        ->where('ud.is_trainee', 0)
                        ->where('UF_ADMIN', 1)
                        ->where('email', $user->email)
                        ->first();
                if(!$localUser) {
                    continue;
                }

                /////////////////////

                
                if($request->has('only_part') && $localUser->full_time == 1) continue;
                if($request->has('only_full') && $localUser->full_time == 0) continue;

                $group = ProfileGroup::find(42);
              

                // if(in_array($localUser->id, $_user_ids)) {
                //     if($request->has('only_pros')) continue;
                    
                // } else {
                //     if($request->has('only_nap')) continue;
                    
                // }
                
                $group_name = '';
                if($localUser->user_type == 'office') {
                    $group_name = "Офисные";
                } else if($localUser->user_type == 'remote'){
                    $group_name = "Удаленные";    
                }  
                

                    ///////////////////////////////

                if($localUser->deleted_at && $localUser->deleted_at != '0000-00-00 00:00:00') {
                    $fired = 1;
                } else {
                    $fired = 0;
                }

                $applied_from = $localUser->workdays_from_applied($date, $group->workdays);

        

                $check = $data->where('employee_id', $localUser->id)->first();
                if($check) {
                    $_decoded = json_decode($check->data);
                    $_decoded->group = $group_name;
                    $_decoded->name = $localUser->name;
                    $_decoded->lastname = $localUser->last_name;
                    $_decoded->email = $localUser->email;
                    $_decoded->full_time = $localUser->full_time;
                    $_decoded->fired = $fired;
                    $_decoded->applied_from = $applied_from;
                    array_push($records, $_decoded);
                } else {
                    array_push($records, [
                        'name' => $localUser->name,
                        'lastname' => $localUser->last_name,
                        'email' => $localUser->email,
                        'group' => $group_name,
                        'full_time' => $localUser->full_time,
                        'id' => $localUser->id,
                        'fired' => $fired,
                        'applied_from' => $applied_from,
                    ]);
                }
            }

            $ignore = $group->workdays == 5 ? [0,6] : [0];
            $workdays = workdays($request->year, $request->month, $ignore);
            $arr = [
                'id' => $activity->id,
                'name' => $activity->name,
                'daily_plan' => $activity->daily_plan,
                'group_id' => $request->group_id,
                'plan_unit' => $activity->plan_unit,
                'workdays' => $workdays,
                'table' => 1,
                'records' => $records,
            ];

            if(in_array($activity->id, [13,15])) {
                $arr['table'] = 0;
            }
            
            $activitiesWithData[] = $arr;
            
        }
        
        if ($activitiesWithData) {
            return json_encode($activitiesWithData);
        }   
        else {
            $array = [
                [
                    'records' => []
                ],
                [
                    'records' => []
                ],
            ];
            return json_encode($array);
        }
    }

    /**
     * Получить сводную таблицу KPI для KASPI 
     */
    public function get_kpi_totals(Request $request)
    {   
        $records = [];
        
        $users = $this->kaspiEmployees($request->group_id);

        $date = Carbon::createFromDate($request->year, $request->month, 1)->format('Y-m-d');


        ///////////////////////////////////

        $_data = AnalyticsSettingsIndividually::where([
            'date' =>  $date, 
            ])
            ->where('group_id', $request->group_id)
            ->whereIn('employee_id', $users)
            ->get();

        

        //////////////////////////////
        
        $month_totals = QualityRecordMonthlyStat::where([
            'month' => $request->month,
            'year' => $request->year,
        ])->get();

        //////////////////////////////////////////////


        $activity1 = Activity::withTrashed()->find(1);
        $activity2 = Activity::withTrashed()->find(2);
        $activity3 = Activity::withTrashed()->find(5);
        $activity4 = Activity::withTrashed()->find(6);
        
        $activity_plan1 = ActivityPlan::where([
            'activity_id' => 1,
            'month' => $request->month,
            'year' => $request->year,
        ])->first();
        
        if($activity_plan1) {
            $activity1->plan_unit = $activity_plan1->plan_unit;
            $activity1->daily_plan = $activity_plan1->plan;
            $activity1->ud_ves = $activity_plan1->ud_ves;
        }

        $activity_plan2 = ActivityPlan::where([
            'activity_id' => 2,
            'month' => $request->month,
            'year' => $request->year,
        ])->first();
        
        if($activity_plan2) {
            $activity2->plan_unit = $activity_plan2->plan_unit;
            $activity2->daily_plan = $activity_plan2->plan;
            $activity2->ud_ves = $activity_plan2->ud_ves;
        }

        $activity_plan3 = ActivityPlan::where([
            'activity_id' => 5,
            'month' => $request->month,
            'year' => $request->year,
        ])->first();
        
        if($activity_plan3) {
            $activity3->plan_unit = $activity_plan3->plan_unit;
            $activity3->daily_plan = $activity_plan3->plan;
            $activity3->ud_ves = $activity_plan3->ud_ves;
        }

        $activity_plan4 = ActivityPlan::where([
            'activity_id' => 6,
            'month' => $request->month,
            'year' => $request->year,
        ])->first();
        
        if($activity_plan4) {
            $activity4->plan_unit = $activity_plan4->plan_unit;
            $activity4->daily_plan = $activity_plan4->plan;
            $activity4->ud_ves = $activity_plan4->ud_ves;
        }
            //////////////////////////////////////  
            
            

            $group = ProfileGroup::find($request->group_id);
            $_user_ids = json_decode($group->users);

        
            //////////////////////////////
        foreach($users as $user) {

            $user_item = [];    


            if($request->group_id == 35) {
                $user_item['group'] = "Напоминание";
                $user_item['activity_id'] = 2;
                $user_item['ocenka'] = $activity2->daily_plan;
            } else if($request->group_id == 42) {
                $user_item['group'] = "Просрочники";    
                $user_item['activity_id'] = 6;
                $user_item['ocenka'] = $activity4 ? $activity4->daily_plan : 100;
            } else {
                $user_item['group'] = "Просрочники";    
                $user_item['activity_id'] = 6;
                $user_item['ocenka'] = $activity4 ? $activity4->daily_plan : 100;
            }


    
            ////////////////////////////////

            
            $user_info = User::withTrashed()->find($user);

            $user_item['name'] = $user_info->last_name . ' ' . $user_info->name;
            $user_item['email'] = $user_info->email;
            
            $user_item['kpi'] = Kpi::userKpi($user_info->id, $date);
            $user_item['employee_id'] = $user_info->id;
            



            ////////////////////////////////////////////


            if($activity1->plan_unit == 'minutes') {
                $user_item['minutes'] = $activity1->daily_plan * $this->workingDays($request->year, $request->month);
            } else {
                $user_item['minutes'] = $activity1->daily_plan;
            }
            
            // if($activity3->plan_unit == 'minutes') {
            //     $user_item['effect'] = $activity3->daily_plan * $this->workingDays($request->year, $request->month);
            // } else {
            //     $user_item['effect'] = $activity3->daily_plan;
            // }
            
            ///////////////////////////////

            $data1 = $_data->where('type', 1);
            $data3 = $_data->where('type', 5);

            $item = $data1->where('employee_id', $user_info->id)->first();
            if($item) {
                $data = json_decode($item->data, true);
                $plan = array_key_exists('plan', $data) ? $data['plan'] : (int)$activity1->plan_unit * 26;
                $user_item['minutes2'] = $plan;
            } else {
                $user_item['minutes2'] = 0;
            }

            $item = $data3->where('employee_id', $user_info->id)->first();
            if($item) {
                $data = json_decode($item->data, true);
                $plan = array_key_exists('plan', $data) ? $data['plan'] : (int)$activity3->plan_unit * 26;
                $user_item['effect2'] = $plan;
            } else {
                $user_item['effect2'] = 0;
            }

            
            
            $_at = $month_totals->where('user_id',$user_info->id)
                ->first();
            $user_item['ocenka2'] = $_at ? $_at->total : 0;
            
            ///////////////////////////////////
            
            array_push($records, $user_item);
            
                
        }
        
        return response()->json([
            'records' => $records
        ]);
    }

    /**
     * НУЖНО УДАЛИТЬ
     */
    public function update_activity_total(Request $request)
    {
        // Временная функция. Заполнялась в апреле 2021
        // $at = ActivityTotal::where([
        //     'date' => $request->date,
        //     'employee_id' => $request->employee_id,
        //     'activity_id' => $request->activity_id,
        // ])->first();
        
        // if($at) {
        //     $at->total = $request->total;
        //     $at->user_id = User::bitrixUser()->id;
        //     $at->save();
        // } else {
        //     ActivityTotal::create([
        //         'date' => $request->date,
        //         'user_id' => User::bitrixUser()->id,
        //         'employee_id' => $request->employee_id,
        //         'total' => $request->total,
        //         'activity_id' => $request->activity_id,
        //     ]);
        // }
    }

    /**
     * Получить количество рабочих дней
     */
    private function workingDays(int $date, int $month) {
        $start = Carbon::create(date('Y'), $month, 1);
        $end = Carbon::create(date('Y'), $month, Carbon::create(date('Y'), $month, 1)->daysInMonth);

        $holidays = [ 
            //Carbon::create(2014, 2, 2),
        ];
        
        $days = 0;
        for($i=1;$i <= Carbon::create(date('Y'), $month, 1)->daysInMonth;$i++) {
            $day = Carbon::create(date('Y'), $month, $i);
            if($day->dayOfWeek != Carbon::SUNDAY && !in_array($day, $holidays)) $days++;
        }

        return $days;
    }

    /**
     * Получить сотрудников KASPI
     * НУЖНО УДАЛИТЬ, использовать функцию в ProfileGroup::class
     */
    private function kaspiEmployees($group_id) {
        $users = []; 
        $groups = ProfileGroup::where('id', $group_id)->get();
        
        foreach($groups as $group) {
            $gr = $group->groupUsers();
            
            foreach($gr as $g) {
                array_push($users, $g->id); 
            }
        }
        $users = array_unique($users);
        return $users;
    }

    /**
     * Экспорт активностей (Подробная аналитика) группы в Excel
     */
    public function exportActivityExcel(Request $request){
        
        $group = ProfileGroup::find($request->group_id);
        
        $request->month = (int) $request->month;
        $currentUser = User::bitrixUser();

        $group_editors = is_array(json_decode($group->editors_id)) ? json_decode($group->editors_id) : [];
        // Доступ к группе
        if ($currentUser->id != 18 && !in_array($currentUser->id, $group_editors)) {
            return [
                'error' => 'access',
            ];
        }

        // $group_accounts = DB::connection('callibro')->table('call_account')
        //         ->where('owner_uid', 5)->whereIn('email', $users_emails)
        //         ->groupBy('call_account.id')
        //         ->orderBy('full_name')
        //         ->get(['id', 'email', 'name', 'surname', DB::raw("CONCAT(surname,' ',name) as full_name")]);
        
        $this->users = User::withTrashed()->whereIn('id', json_decode($group->users))
        ->get(['ID as id', 'email as email', 'name as name', 'last_name as surname', DB::raw("CONCAT(last_name,' ',name) as full_name")]);;

        /****************************** */
        /******==================================== */
        $date = Carbon::createFromDate($request->year, $request->month, 1);


        $title = 'Аналитика активностей ' . $request->month . ' месяц '. $request->year;

        if(in_array($request->group_id, [35,42,56])) { // Kaspi
            $data = json_decode($this->getActivities($request), true);
            $title = 'Аналитика активностей KASPI ' . $request->month. ' месяц '. $request->year;
        } else {
            $data = json_decode($this->getActivities($request), true);
        }

        /******==================================== */
        
        $sheets = [];

        $minute_headings = Activity::getHeadings($date, Activity::UNIT_MINUTES);
        $percent_headings = Activity::getHeadings($date, Activity::UNIT_PERCENTS);
      
        if($request->group_id == DM::GROUP_ID) {
     
            $sheets = [
                [
                    'title' => 'Сводная таблица',
                    'headings' => AnalyticsSettings::getHeadings(DM::GROUP_ID, $date),
                    'sheet' => AnalyticsSettings::getSheet(DM::GROUP_ID, $date)
                ], 
                [
                    'title' => 'Часы работы',
                    'headings' => $minute_headings,
                    'sheet' => Activity::getSheet($data[0]['records'], $date, Activity::UNIT_MINUTES)
                ],
                [
                    'title' => 'Количество действий',
                    'headings' => $minute_headings,
                    'sheet' => Activity::getSheet($data[1]['records'], $date, Activity::UNIT_MINUTES)
                ],
                [
                    'title' => 'Учет времени',
                    'headings' => $minute_headings,
                    'sheet' => Activity::getSheet($data[2]['records'], $date, Activity::UNIT_MINUTES)
                ]
            ];
        } else if(in_array($request->group_id, Ozon::GROUP_ID)) {
            $sheets = [
                [
                    'title' => 'Часы работы',
                    'headings' => $minute_headings,
                    'sheet' => Activity::getSheet($data[0]['records'], $date, Activity::UNIT_MINUTES)
                ],
                [
                    'title' => 'Количество тикетов',
                    'headings' => $minute_headings,
                    'sheet' => Activity::getSheet($data[1]['records'], $date, Activity::UNIT_MINUTES)
                ]
            ];
        } else if(in_array($request->group_id, Lerua::GROUP_ID)) {
            $sheets = [
                [
                    'title' => 'Минуты',
                    'headings' => $minute_headings,
                    'sheet' => Activity::getSheet($data[0]['records'], $date, Activity::UNIT_MINUTES)
                ],
                [
                    'title' => 'Закрыто тикетов',
                    'headings' => $minute_headings,
                    'sheet' => Activity::getSheet($data[1]['records'], $date, Activity::UNIT_MINUTES)
                ]
            ];
        } else if(in_array($request->group_id, [53])) { 
            $sheets = [];
            foreach($data as $item) {
                $_headings = $item['plan_unit'] == 'minutes' ? $minute_headings : $percent_headings;
                $_units = $item['plan_unit'] == 'minutes' ? Activity::UNIT_MINUTES : Activity::UNIT_PERCENTS;
                $sheets[] = [
                    'title' => $item['name'],
                    'headings' => $_headings,
                    'sheet' => Activity::getSheet($item['records'], $date, $_units),
                ];
            }
        } else if(in_array($request->group_id, HomeCredit::GROUP_ID)) {
            $sheets = [
                [
                    'title' => 'Минуты',
                    'headings' => $minute_headings,
                    'sheet' => Activity::getSheet($data[0]['records'], $date, Activity::UNIT_MINUTES)
                ],
                [
                    'title' => 'Согласия',
                    'headings' => $minute_headings,
                    'sheet' => Activity::getSheet($data[1]['records'], $date, Activity::UNIT_MINUTES)
                ]
            ];
        }  else if(in_array($request->group_id, Tinkoff::GROUP_ID)) {
            $sheets = [
                [
                    'title' => 'Минуты',
                    'headings' => $minute_headings,
                    'sheet' => Activity::getSheet($data[0]['records'], $date, Activity::UNIT_MINUTES)
                ],
                [
                    'title' => 'Согласия',
                    'headings' => $minute_headings,
                    'sheet' => Activity::getSheet($data[1]['records'], $date, Activity::UNIT_MINUTES)
                ]
            ];
        } else {
            $sheets = [
                [
                    'title' => 'Минуты разговора',
                    'headings' => $minute_headings,
                    'sheet' => Activity::getSheet($data[0]['records'], $date, Activity::UNIT_MINUTES)
                ],
                [
                    'title' => 'Количество сбора',
                    'headings' => Activity::getHeadings($date, Activity::UNIT_MINUTES, true),
                    'sheet' => array_key_exists(1, $data) ? Activity::getSheet($data[1]['records'], $date, Activity::UNIT_MINUTES, true) : []
                ]
            ];

        }
       

        /******==================================== */

        if(ob_get_length() > 0) ob_clean(); //  ob_end_clean();

        if($date->daysInMonth == 28) $last_cell = 'AH3';
        if($date->daysInMonth == 29) $last_cell = 'AI3';
        if($date->daysInMonth == 30) $last_cell = 'AJ3';
        if($date->daysInMonth == 31) $last_cell = 'AK3';

        Excel::create($title, function ($excel) use ($sheets, $last_cell) {
            $excel->setTitle('Отчет');
            $excel->setCreator('Laravel Media')->setCompany('MediaSend KZ');
            $excel->setDescription('экспорт данных в Excel файл');
            
            foreach($sheets as $page) {
                $excel->sheet($page['title'], function ($sheet) use ($page, $last_cell) {
                
                    $sheet->fromArray($page['sheet'], null, 'A4', false, false);
                    $sheet->prependRow(3, $page['headings']);
                    
                    $sheet->cell('A1', function($cell) use ($page){
                        $cell->setValue($page['title']);
                        $cell->setFontWeight('bold'); 
                        $cell->setFontSize(14); 
                    });

                    $sheet->cell('A3:'. $last_cell, function($cell) {
                        $cell->setBackground('#8ccf5b');  
                        $cell->setFontWeight('bold');
                    });
                });
            }
            
            $excel->setActiveSheetIndex(0);
        })->export('xlsx');
        
    }

    /**
     * Поменять профиль рекрутера в почасовой таблице
     * Для сотрудников группы 48 Рекрутинг 
     */
    public function changeRecruiterProfile(Request $request){
        RecruiterStat::changeProfile($request['user_id'], $request['profile'], Carbon::createFromDate($request->year, $request->month, $request->day));
    }

    
}

