<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuartalBonus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Mail as Mailable;
use Illuminate\Mail\Mailer;
use App\Models\Analytics\UserStat;
use App\Models\Analytics\Activity;
use Swift_Mailer;
use Swift_SmtpTransport;
use Swift_TransportException;
use Illuminate\Http\Request;
use Auth;
use App\Kpi;
use App\Salary;
use Carbon\Carbon;
use App\Models\Admin\Bonus;
use App\Downloads;
use App\Account;
use App\UserNotification;
use App\PositionDescription;
use App\Position;
use App\Program;
use App\WorkingDay;
use App\WorkingTime;
use App\ProfileGroup;
use App\Setting;
use App\DayType;
use App\User;
use App\UserExperience;
use App\Trainee;
use App\UserDescription;
use App\UserDeletePlan;
use App\UserContact;
use App\Zarplata;
use App\BookCategory;
use App\TimetrackingHistory;
use App\Photo;
use App\UserAbsenceCause;
use App\Models\Admin\ObtainedBonus;
use App\External\Bitrix\Bitrix;
use App\Models\Bitrix\Lead;
use App\Models\Bitrix\Segment;
use App\Http\Controllers\IntellectController as IC;
use App\Classes\Helpers\Phone;
use App\Classes\Analytics\Recruiting as RM;
use App\Models\Analytics\RecruiterStat;
use App\AnalyticsSettings;
use App\AnalyticsSettingsIndividually;
use App\Models\CallCenter\Directory;
use App\Models\CallCenter\Agent;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Admin\History;
use App\UserReport;
use App\Models\User\NotificationTemplate;
use App\Models\User\Card;
use App\Classes\Helpers\Currency;
use App\Models\Admin\EditedBonus;
use App\Models\Admin\EditedKpi;
use App\Classes\UserAnalytics;
use App\QualityRecordWeeklyStat;
use App\Http\Controllers\Admin\GroupAnalyticsController as GAController;
use App\Models\Analytics\IndividualKpi;
use App\Models\Analytics\TraineeReport;
use App\AdaptationTalk;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public $roles = [
        "page1" => null,
        "page2" => null,
        "page3" => null,
        "page4" => null,
        "page5" => null,
        "page6" => null,
        "page7" => null,
        "page8" => null,
        "page9" => null,
        "page10" => null,
        "page11" => null,
        "page12" => null,
        "page13" => null,
        "page14" => null,
        "page15" => null,
        "page16" => null,
        "page17" => null,
        "page18" => null,
        "page19" => null,
        "page20" => null,
        "page21" => null, // Учет времени
        "page22" => null,
    ];

    public function surv(Request $request)
    {
        View::share('menu', 'timetrackinguser');
        return view('test');
    }

    function quarter(\DateTime $dateTime){
        return (int) ceil($dateTime->format('n') / 3);
    }


    public function profile(Request $request)
    {

       

        $user = Auth::user();


        $d1 = date('Y-m-d');
        $kv = intval((date('m', strtotime($d1)) + 2)/3);

        $quartal = QuartalBonus::on()->where('user_id',$user->id)
            ->where('year',date('Y'))
            ->where('quartal',$kv)
            ->get()->toArray();

        $quarter_bonus = QuartalBonus::on()->where('user_id',$user->id)
            ->where('year',date('Y'))
            ->where('quartal',$kv)
            ->sum('sum');








        if($user->id == 5 && isset($_GET['user_id'])) {
            $user = User::find($_GET['user_id']);
        }

        $new_email = trim(strtolower($request->email));

        /******* Смена пароля */
        if($request->isMethod('post')) {

           
            if($user->email != $new_email) {  // Введен новый email
                
                $checkEmail = User::where('email', $new_email)->first();
           
                if($checkEmail) {
                    return redirect()->back()->withErrors(['Введенный E-mail уже занят: ' . $new_email]);
                } else {

                    $user->email = $new_email;
                    $user->save();

                    $account = Account::where('owner_uid', 5)->where('email', $new_email)->first();

                    // if(!$account) {
                        
                    //     $account = Account::create([
                    //         'password' => User::randString(16),
                    //         'owner_uid' => 5,
                    //         'name' => $user->name,
                    //         'surname' => $user->last_name,
                    //         'email' => $new_email,
                    //         'status' => Account::ACTIVE_STATUS,
                    //         'role' => [Account::OPERATOR],
                    //         'activate_key' => '000',
                    //     ]);

                    //     $agent_name = $account->id . '@voip.cfpsa.ru';
                    //     $agent = Agent::where('name', $agent_name)->first();
                    //     if(!$agent) {
                    //         $agent = Agent::create([
                    //             'name' => $agent_name,
                    //             'system' => 'single_box',
                    //             'type' => 'callback',
                    //             'contact' => Agent::CONTACT_PREFIX . $agent_name,
                    //             'status' => 'Logged Out',
                    //             'state' => 'Waiting'
                    //         ]);
                    //     }
                        
                    //     $directory = Directory::where('account', $account->id)->first();
                    //     if(!$directory) {
                    //         $directory = Directory::create([
                    //             'account' => $account->id,
                    //             'password' => $account->password,
                    //             'domain' => 'voip.cfpsa.ru',
                    //             'context' => 'voip.cfpsa.ru_context',
                    //             'provider' => '600',
                    //             'toll_allow' => '600',
                    //             'state' => 'active',
                    //         ]);
                    //     } else {
                    //         $directory->password = $account->password;
                    //         $directory->toll_allow = '600';
                    //         $directory->provider = '600';
                    //         $directory->domain = 'voip.cfpsa.ru';
                    //         $directory->context = 'voip.cfpsa.ru_context';
                    //         $directory->state = 'active';
                    //         $directory->save();
                    //     }
                    // }
        
                }
               
            } 
            
            if($request->currency != $user->currency 
             && in_array(strtoupper($request->currency), ['KZT', 'RUB', 'UZS', 'KGS','BYN', 'UAH'])){
                $user->currency = strtolower($request->currency);
                $user->save();
            } 

            
            if(!empty($request->password)) { // Введен новый пароль
                
                $salt = User::randString(8);
                $password = $salt.md5($salt.$request->password);
                $user->PASSWORD = $password;
                $user->save();

                Auth::logout();

                return redirect()->back();
            } 
            
            return redirect()->back();
            
        } else { // GET запрос

            $positions = Position::all();
            $photo = Photo::where('user_id', $user->id)->first();
            $downloads = Downloads::where('user_id', $user->id)->first();
            $user_position = Position::find($user->position_id);

            /*** Группы пользователя */
            $groups = '';
            $_groups = ProfileGroup::where('active', 1)->get();
            
            $gs = [];
            foreach($_groups as $group) {
                if($group->users == null) {
                    $group->users = '[]';
                }
                $group_users = json_decode($group->users);
                
                if(in_array($user->id, $group_users)) {
                    array_push($gs, $group);
                    $groups .= '<div>' . $group->name . '</div>';
                }
            }

            /*** Текущая книга для прочтения */
            $book = app('App\Http\Controllers\Admin\ExamController')->currentBook($user->id, date('m'), date('Y'));
            

            /* recruiter */
            $rec_group = ProfileGroup::find(48);




            if($rec_group->users == null) {
                $rec_group->users = '[]';
            }

                $rg_users = json_decode($rec_group->users);

                $recruiter_stats = json_encode([]);
                $recruiter_records = json_encode([]);

                if(in_array($user->id, $rg_users)) {
                    $is_recruiter = true;
                    $recruiter_stats = json_encode(RecruiterStat::tables(date('Y-m-d')));

                    $asi  = AnalyticsSettingsIndividually::whereYear('date', date('Y'))->whereMonth('date', date('m'))
                        ->where('group_id', RM::GROUP_ID)
                        ->where('employee_id', $user->id)
                        ->first();

                        if($asi) {
                            $recruiter_records = json_decode($asi->data);
                        }  else {
                            $rm = new RM();
                            $recruiter_records = $rm->defaultUserTable($user->id)['records'];
                        }
                    
                    $indicators = $this->recruiting_temp();
                } else {
                    $is_recruiter = false;
                    $indicators = json_encode([]);
                }

            $head_in_groups = [];
            $trainee_report = [];
            if($user->position_id == 45 || $user->position_id == 55) {
                if($user->position_id == 45) {
                    $head_in_groups = $user->headInGroups();     
                }else {
                    $head_in_groups = $user->inGroups();
                }
                
                $x = []; 
                foreach($head_in_groups as $xgroup) {
                    $x[] = $xgroup->id;
                }
                $trainee_report = TraineeReport::getBlocks(date('Y-m-d'), $x);

            }
            

            foreach($head_in_groups as $group) {
                if(Carbon::parse($group->checktime)->timestamp - time() >= 0) {
                    $group->checktime = Carbon::parse($group->checktime)->setTimezone('Asia/Almaty');
                } else {
                    $group->checktime = null;
                }
            }
            ///////////////////////////////////////
   
            Carbon::setLocale('ru');
            $month = [
                'daysInMonth' => Carbon::now()->daysInMonth,
                'currentMonth' => Carbon::now()->format('F')
            ];

            ////
            $recruiter_stats_rates = [];

            for ($i = 1; $i <= Carbon::now()->daysInMonth; $i++) {
                $rec = new RM();
                $value = $rec->getOnlineRates(Carbon::now()->day($i)->format('Y-m-d'));
                $recruiter_stats_rates[$i] = $value;
            }
            $recruiter_stats_rates = json_encode($recruiter_stats_rates);

            $zarplata = Zarplata::where('user_id', $user->id)->first();
            $oklad = 0;
            if($zarplata) $oklad = $zarplata->zarplata;

            try {
                $currency_rate = (float)Currency::rates()[$user->currency];
            } catch(\Exception $e) {
                $currency_rate = 0.00001;
            }
            $oklad = round($oklad * $currency_rate, 0);

            // rate
            
            $currency_rate = in_array($user->currency, array_keys(Currency::rates())) ? (float)Currency::rates()[$user->currency] : 0.0000001;

            //bonuses
            $bonuses = Salary::where('user_id', $user->id)
                ->whereYear('date',  date('Y'))
                ->whereMonth('date', date('m'))
                ->where(function($query) {
                    $query->where('award', '!=', 0)
                        ->orWhere('bonus', '!=', 0);
                })
                ->orderBy('id','desc')
                ->get();
            
            $bonus = $bonuses->sum('bonus');
            $bonus += ObtainedBonus::onMonth($user->id, date('Y-m-d'));

            $bonusHistory = ObtainedBonus::getHistory($user->id, date('Y-m-d'), $currency_rate);

            
            // Бонусы 

            $editedBonus = EditedBonus::where('user_id', $user->id)
                ->whereYear('date',  date('Y'))
                ->whereMonth('date',  date('m'))
                ->first();
            $bonus = $editedBonus ? $editedBonus->amount : $bonus;

            /**
             * EARNINGS COMPONENT
             */
            $editedKpi = EditedKpi::where('user_id', $user->id)
                ->whereYear('date', date('Y'))
                ->whereMonth('date', date('m'))
                ->first();

            if($editedKpi) {
                $kpi = $editedKpi->amount;
            } else {
                $kpi = Kpi::userKpi($user->id);
            }   

            $salary = $user->getCurrentSalary();
            
            $potential_bonuses = '';
            if(count($gs) > 0) {
                foreach ($gs as $key => $g) {
                    $potential_bonuses .= Bonus::getPotentialBonusesHtml($g->id);
                    $potential_bonuses .= '<br>';
                }
            }
            
            // check exists ind kpi
            $kpis = $user->inGroups();
            $ind_kpi = IndividualKpi::where('user_id', $user->id)->first();
            if($ind_kpi) {
                $kpis = [[
                    'name' => 'Условия расчета KPI',
                    'type' => 'individual',
                    'id' => 0,
                ]];
            } else {
                foreach ($kpis as $key => $kp) {
                    $kp->type = 'common';
                }
            }


            // prepare user_earnigs 
            $oklads = number_format(round((float)$oklad * $currency_rate), 0, '.', '\'') . ' ' . strtoupper($user->currency);
            $user_earnings = [
                'quarter_bonus' => $quarter_bonus.' '. strtoupper($user->currency),
                'oklad' => round((float)$oklad * $currency_rate, 0),
                'bonus' => number_format(round((float)$bonus * $currency_rate), 0, '.', '\'') . ' ' . strtoupper($user->currency),
                'kpis' => $kpis,
                'bonusHistory' => $bonusHistory,
                'editedBonus' => $editedBonus,
                'editedKpi' => $editedKpi,
                'potential_bonuses' => $potential_bonuses,
                'salary_percent' => $oklad > 0 ? $salary / $oklad * 100 : 0,
                'kpi_percent' => $kpi / 400, // kpi / 40000 * 100
                'kpi' => number_format((float)$kpi * $currency_rate,  0, '.', '\''). ' ' . strtoupper($user->currency),
                'salary' => number_format((float)$salary * $currency_rate, 0, '.', '\''). ' ' . strtoupper($user->currency),
                'salary_info' => [
                    'worked_days' => $user->worked_days(),
                    'indexation_sum' => $user_position ? $user_position->sum : 0,
                    'days_before_indexation' => $user->days_before_indexation(),
                    'oklad' => $oklads
                ]
            ];

            $oklad = number_format($oklad, 0, '.', ' ');

            // 
            $request = new Request();
            $request->year = date('Y');
            $request->month = date('m');

            $activities = '[]';
            $quality = [];
            if(count($gs) > 0) {
                $request->group_id = $gs[0]->id;
                $_activities = Activity::where('group_id', $gs[0]->id)->first();
                
                $activities = UserStat::activities($gs[0]->id , date('Y-m-d'));
                    $activities = json_encode($activities);

                $users_ids = json_decode($gs[0]->users);

                $quality = $_activities ? QualityRecordWeeklyStat::table($users_ids, date('Y-m-d')) : [];
                
                
            }   
            
            $show_payment_terms = false;
            foreach ($gs as $key => $gr) {
                if($gr->payment_terms && $gr->payment_terms != '' && $gr->show_payment_terms == 1) {
                    $show_payment_terms = true;
                }
            }

            $blocks_number = 1;

            $position_desc = PositionDescription::where('position_id', $user->position_id)->first();
            if($position_desc && $position_desc->show == 1) $blocks_number++;
            if($show_payment_terms) $blocks_number++;


            /////////////////////////////////////
            //View::share('title', 'Мой профиль'); 
            View::share('menu', 'profile');


//            dd('asd1');


           $mycourse = \App\Models\CourseResult::whereIn('status', [0,2])
               ->where('user_id', $user->id)
               ->orderBy('status', 'desc')
               ->with('course')
               ->first();



         
            return view('admin.timetracking', compact('user', 'oklad','positions', 'user_position', 'photo', 
                'downloads', 'groups', 'book', 'is_recruiter', 'indicators', 'month', 
                'recruiter_stats', 'recruiter_stats_rates', 'recruiter_records', 'head_in_groups',
                'user_earnings','quartal'))->with([
                    'answers' => UserExperience::getAnswers($user->id),
                    'position_desc' => $position_desc,
                    'groups_pt' => $gs,
                    'show_payment_terms' => $show_payment_terms,
                    'blocks_number' => $blocks_number,
                    'activities' => $activities,
                    'quality' => $quality,
                    'trainee_report' => $trainee_report,
                    'course' => $mycourse,
                ]);
        }
        
        
    }

    public function recruiting_temp() {
        $month = Carbon::createFromFormat('m-Y', date('m') . '-' . date('Y'));
        $group = ProfileGroup::find(48);

        $helper = new RM();

        $indicators = []; // Для визуальных данных под сводной таблицей
      
        $settings = AnalyticsSettings::whereYear('date', date('Y'))->whereMonth('date', date('m'))
            ->where('group_id', RM::GROUP_ID)
            ->where('type', 'basic')
            ->first();

        if($settings) {
            $arr = $settings->data;

            $indicators['info']['created'] = $arr[RM::S_CREATED]['fact']; 
            $indicators['info']['converted'] = $arr[RM::S_CONVERTED]['fact']; 

            $trainees = DayType::whereYear('date', date('Y'))->whereMonth('date', date('m'))->whereDay('date', date('d'))->where('type', 5)->get()->pluck('user_id')->toArray();
            $trainees = array_unique($trainees);
            $indicators['info']['trainees'] = count($trainees);

            $indicators['info']['applied'] = $arr[RM::S_APPLIED]['fact']; 
            $indicators['info']['remain_apply'] = $arr[RM::S_APPLIED]['plan'] - $arr[RM::S_APPLIED]['fact']; 

            $x_count = User::leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('ud.is_trainee', 0)
                ->where('UF_ADMIN', 1)
                ->get()
                ->count();

            $indicators['info']['working'] = $x_count;

            $training_users = Trainee::whereNull('applied')->pluck('user_id')->toArray();
            $t_users = User::leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('ud.is_trainee', 1)
                ->where('UF_ADMIN', 1)
                ->get();


            $trainees = DayType::whereYear('date', date('Y'))->whereMonth('date', date('m'))->whereDay('date', date('d'))->whereIn('type', [5,7])->get()->pluck('user_id')->toArray();
            $trainees = array_unique($trainees);

            $indicators['info']['training'] = count($trainees);
            
            $indicators['info']['fired'] = $arr[RM::S_FIRED]['fact']; 
            $indicators['info']['applied_plan'] = $arr[RM::S_APPLIED]['plan']; 
            $indicators['info']['trainees_plan'] = $arr[RM::S_TRAINING_TODAY]['plan']; 

            $indicators['today'] = date('d');
            $indicators['month'] = (int)date('m');
        }
        
        /////////////////////
        $t = RM::getTableRecruiters(json_decode($group->users), ['year' => date('Y'), 'month' => date('m')]);
        $indicators['recruiters'] = $t['recruiters'];

        /// Заказы руководителей
        $orders = [];
        $orderGroups = ProfileGroup::where('active', 1)->get(); 
        foreach ($orderGroups as $group) {
            $orders[] = [
                'group' => $group->name,
                'required' => $group->required,
                'fact' => $group->provided . ' ',
            ];
        }
        
        $indicators['orders'] = $orders;

        /////////////////// Count remain days
        $start = Carbon::now();
        $end = Carbon::now()->setDate(date('Y'), date('m'), 1)->endOfMonth();
        
        $holidays = [
        //     Carbon::create(2014, 2, 2),
        ];

        
        if($end->timestamp - $start->timestamp >= 0 && $end->month >= date('m')) {
            $remain_days = $start->diffInDaysFiltered(function (Carbon $date) use ($holidays) {
                return !$date->isDayOfWeek(Carbon::SUNDAY); //&& !in_array($date, $holidays);
            }, $end);
        } else {
            $remain_days = 0;
        }

        $indicators['info']['remain_days'] = $remain_days;

        
        return json_encode($indicators);
    }

    public function changePassword(Request $request) {

        $user = Auth::user();

        if(!empty($request->password) && $request->password == $request->repassword) {
            $salt = User::randString(8);
            $password = $salt.md5($salt.$request->password);
            $user->PASSWORD = $password;
            $user->save();

            Auth::logout();
            
            return [
                'code' => 200
            ];   
        } else {
            return [
                'code' => 500
            ];   
        }
    }

    public function getpersons(Request $request)
    {
        //$accounts = DB::connection('callibro')->table('call_account')->where('owner_uid', 5)->get(['id', 'email']);
        $groups = ProfileGroup::where('active', 1)->get();
        // $array_accounts_email = [];
        // foreach ($accounts as $key => $account) {
        //     $array_accounts_email[] = $account->email;
        // }

        if (isset($request['filter']) && $request['filter'] == 'all') {

            //$users = User::withTrashed()->whereIn('email', $array_accounts_email);

            $users = User::withTrashed()
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('UF_ADMIN', 1);

            if ($request['start_date']) $users = $users->whereDate('DATE_REGISTER', '>=', $request['start_date']);
            if ($request['end_date']) $users = $users->whereDate('DATE_REGISTER', '<=', $request['end_date']);
            if ($request['start_date_deactivate']) $users = $users->whereDate('deactivate_date', '>=', $request['start_date_deactivate']);
            if ($request['end_date_deactivate']) $users = $users->whereDate('deactivate_date', '<=', $request['end_date_deactivate']);

            if ($request['start_date_applied']) $users = $users->whereDate('applied', '>=', $request['start_date_applied']);
            if ($request['end_date_applied']) $users = $users->whereDate('applied', '<=', $request['end_date_applied']);

            if ($request['segment'] != 0) $users = $users->where('segment', $request['segment']);
            
            
            // Стажеров показывал но долго грузит
            // $trainees = Trainee::whereNull('applied')->get();
            // foreach($users as $user) {
            //     $trainee = $trainees->where('user_id', $user->id);
            //     if($trainee->count() > 0) {
            //         $user->is_trainee = true;
            //     } else {
            //         $user->is_trainee = false;
            //     }
            // }
        } elseif(isset($request['filter']) && $request['filter'] == 'deactivated') {

            //$users = User::onlyTrashed()->whereIn('email', $array_accounts_email); 
            $users = User::onlyTrashed()
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('UF_ADMIN', 1)
                ->where('is_trainee', 0);
            
            if ($request['start_date_deactivate']) $users = $users->whereDate('deactivate_date', '>=', $request['start_date_deactivate']);
            if ($request['end_date_deactivate']) $users = $users->whereDate('deactivate_date', '<=', $request['end_date_deactivate']);
            if ($request['segment'] != 0) $users = $users->where('segment', $request['segment']);
            // $trainees = Trainee::whereNull('applied')->get()->pluck('user_id')->toArray();
            // $users = $users->whereNotIn('users.id', $trainees);
            
        } elseif(isset($request['filter']) && $request['filter'] == 'nonfilled') {

            $users_1 = User::where('UF_ADMIN', 1)
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('is_trainee', 0)
                ->get(['users.id'])
                ->pluck('id')
                ->toArray();

            $downloads = Downloads::whereIn('user_id', array_unique($users_1))
                ->get(['user_id'])
                ->pluck('user_id')
                ->toArray(); 
            
            $users_1 = array_diff($users_1 ,array_unique($downloads));
            
            $users = User::where('UF_ADMIN', 1)
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('is_trainee', 0)
                ->where(function($query){
                    $query->whereNull('users.position_id')
                            ->orWhereNull('users.PHONE')
                            ->orWhereNull('users.phone_1')
                            ->orWhereNull('users.phone_2')
                            ->orWhereNull('users.phone_3')
                            ->orWhereNull('users.birthday')
                            ->orWhereNull('users.working_day_id')
                            ->orWhereNull('users.working_time_id');
                })
                ->orWhere('is_trainee', 0)
                ->whereIn('users.id', array_values($users_1));
            

        } elseif(isset($request['filter']) && $request['filter'] == 'trainees') {

            $users = User::leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('UF_ADMIN', 1)
                ->where('is_trainee', 1);
            
            if ($request['start_date']) $users = $users->whereDate('DATE_REGISTER', '>=', $request['start_date']);
            if ($request['end_date']) $users = $users->whereDate('DATE_REGISTER', '<=', $request['end_date']);
            if ($request['start_date_deactivate']) $users = $users->whereDate('deactivate_date', '>=', $request['start_date_deactivate']);
            if ($request['end_date_deactivate']) $users = $users->whereDate('deactivate_date', '<=', $request['end_date_deactivate']);
            
            
        } else {

            $users = User::leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('UF_ADMIN', 1)
                ->where('is_trainee', 0);
            
           // $trainees = Trainee::whereNull('applied')->get()->pluck('user_id')->toArray();
            if ($request['start_date']) $users = $users->whereDate('DATE_REGISTER', '>=', $request['start_date']);
            if ($request['end_date']) $users = $users->whereDate('DATE_REGISTER', '<=', $request['end_date']);
            if ($request['segment']) $users = $users->where('segment', $request['segment']);

            if ($request['start_date_applied']) $users = $users->whereDate('applied', '>=', $request['start_date_applied']);
            if ($request['end_date_applied']) $users = $users->whereDate('applied', '<=', $request['end_date_applied']);
        }


        $users = $users->get([
            'users.id', 
            'users.email',  
            'users.user_type',
            'users.segment as segment',
            'users.last_name',
            'users.name', 
            'users.full_time', 
            DB::raw("CONCAT(users.last_name,' ',users.name) as FULLNAME"), 
            DB::raw("CONCAT(users.name,' ',users.last_name) as FULLNAME2"),
            'users.DATE_REGISTER',
            'users.deactivate_date',
            'users.last_group',
            'users.position_id',
            'users.PHONE',
            'users.phone_1',
            'users.phone_2',
            'users.birthday',
            'users.DESCRIPTION',
            'users.working_day_id',
            'users.working_time_id',
            'users.work_start',
            'users.work_end',
            'users.program_id',
            'ud.fire_cause',
            'ud.applied',
        ]);

        foreach ($users as $key => $user) {

            $user->groups = [];
            $user_groups = [];
            
            foreach ($groups as $group) {
                $group_users = json_decode($group->users);
                if (!is_array($group_users)) {
                    continue;
                }

                if (in_array($user->id, $group_users)) {
                    $user_groups[] = $group->id;
                }
            }

            $user->groups = $user_groups;
            
            if(is_null($user->deactivate_date) || $user->deactivate_date == '0000-00-00 00:00:00') {
                $user->deactivate_date = '';
            } else {
                $user->deactivate_date = Carbon::parse($user->deactivate_date)->addHours(6)->format('Y-m-d H:i:s');
                if($user->deactivate_date == '30.11.-0001 00:00:00') {
                    $user->deactivate_date = '';
                } 
            }

            
            
            // if(is_null($user->has_trainee)) {
            //     $user->applied = $user->DATE_REGISTER;
            // } else if(time() - Carbon::parse($user->applied)->timestamp <= 60) {
                
            //         $users->applied = null;
            // } 

            if ($request['start_date_applied'] != null &&
                Carbon::parse($user->applied)->timestamp - Carbon::parse($request['start_date_applied'])->timestamp < 0) {
                $users->forget($key);
                continue;
            } 

            if ($request['end_date_applied'] != null &&
                Carbon::parse($user->applied)->timestamp - Carbon::parse($request['end_date_applied'])->timestamp > 0) {
                $users->forget($key);
                continue;
            } 
            

            $user->DATE_REGISTER = Carbon::parse($user->DATE_REGISTER)->addHours(6)->format('Y-m-d H:i:s');
    
            if($user->applied) {
                $user->applied = Carbon::parse($user->applied)->addHours(6)->format('Y-m-d H:i:s');
            }   
            

            if (isset($request['filter']) && $request['filter'] == 'deactivated' && $user->last_group != '[]') {
                $user->groups = json_decode($user->last_group);
            } elseif($user->deleted_at && $user->last_group != '[]') {
                $user->groups = json_decode($user->last_group);
            }


        }


        ////////////////////////
       
        $groups = $groups->pluck('name', 'id')->toArray();

        if($request->excel) {
            $data['records'] = [];

            $headings = [
                'id',
                'ФИО',
                'Email',
                'Группы',
                'Тип',
                'Full/Part',
                'Сегмент',
                'Должность',
                'Дата регистрации',
                'Дата принятия',
                'Дата увольнения',
                'Причина увольнения',
                'Телефон',
                'Тел. 2',
                'Тел. 3',
                'День рождения',
                'Доп.',
                'Программа',
                'График',
                'Часы работы',
                'Начало',
                'Конец',
            ];

            
            
            $segments = Segment::get();
            $positions = Position::get()->pluck('position', 'id')->toArray();
            foreach($users as $user) {
                $seg = $segments->where('id', $user->segment)->first();
                $segment = $seg ? $seg->name : $user->segment;
               // dump($user->segment);
                
                $grs = '';
                foreach($user->groups as $gr) {
                    try {
                        $grs .= $groups[$gr] . '  ';
                    } catch(\Exception $e) {
                        $grs .= $gr . '  ';
                    }   
                }

                if($user->last_group) {
                    foreach(json_decode($user->last_group) as $gr) {
                        try {
                            $grs .= $groups[$gr] . '  ';
                        } catch(\Exception $e) {
                            $grs .= $gr . '  ';
                        }   
                    }
                }
                
                
                $data['records'][] = [
                    0 => $user->id,
                    1 => $user->last_name . ' ' . $user->name, 
                    2 => $user->email, 
                    3 => $grs, 
                    4 => $user->user_type == 'office' ? 'Офисный' : 'Удаленный', 
                    5 => $user->full_time == 1 ? 'Full-time' : 'Part-time', 
                    6 => $segment, 
                    7 => array_key_exists($user->position_id, $positions) ? $positions[$user->position_id] : $user->position_id, 
                    8 => $user->DATE_REGISTER, 
                    9 => $user->applied, 
                    10 => $user->deactivate_date, 
                    11 => $user->fire_cause, 
                    12 => $user->PHONE, 
                    13 => $user->phone_1, 
                    14 => $user->phone_2, 
                    15 => $user->birthday, 
                    16 => $user->DESCRIPTION, 
                    17 => $user->program_id == 1 ? "U-Calls" : 'Другое', 
                    18 => $user->working_day_id == 1 ? '5-2' : '6-1', 
                    19 => $user->working_time_id == 1 ? 8 : 9, 
                    20 => $user->work_start, 
                    21 => $user->work_end, 
                ];    
            }

           //dd(1);
            ob_end_clean();
            if (ob_get_length() > 0) ob_clean();
            
            return Excel::create('Сотрудники '. date('Y-m-d'), function ($excel) use ($data, $headings) {
                $excel->setTitle('Отчет');
                $excel->setCreator('Laravel Media')->setCompany('MediaSend KZ');
                $excel->setDescription('Экспорт данных в Excel файл');
                $excel->sheet('Сотрудники', function ($sheet) use ($data, $headings) {
                    $sheet->fromArray($data['records'], null, 'A1', false, false);
                    $sheet->prependRow(1, $headings);
                });
            })->export('xls');
        }   
            
        
            
        $users = $users->values();
        


        ////////////////////

        return [
            'users' => $users,
            'can_login_users' => [5,18],
            'auth_token' => Auth::user()->remember_token,
            'currentUser' => Auth::user()->id,
            'segments' => Segment::pluck('name', 'id'),
            'groups' => [0 => 'Выберите группу'] + $groups,
            'start_date' => Carbon::now()->startOfMonth()->format('Y-m-d'),
            'end_date' => Carbon::now()->endOfMonth()->format('Y-m-d'),
        ];
    }
    
    public function setNotiRead(Request $request) {

        $noti = UserNotification::where('user_id', Auth::user()->id)->where('id', $request->id)->first();
        if($noti) {
            $timestamp = $noti->group;

            $noti->read_at = now();
            if($request->comment) {
                $noti->note = $request->comment;

                if($noti->about_id != 0) {
                    $type = UserAbsenceCause::THIRD_DAY;
                    if($noti->title == 'Пропал с обучения: 1 день') $type = UserAbsenceCause::FIRST_DAY;
                    if($noti->title == 'Пропал с обучения: 2 день') $type = UserAbsenceCause::SECOND_DAY;
                    UserAbsenceCause::createOrUpdate([
                        'user_id' => $noti->about_id,
                        'date' => Carbon::now()->day(1)->format('Y-m-d'),
                        'type' => $type,
                        'text' => $request->comment,
                    ]);
                }
                
            }
            $noti->save();

            if($noti->about_id != 0) {
                $copies = UserNotification::where('group', $timestamp)->get();

                foreach ($copies as $copy) {
                    $copy->read_at = now();
                    $copy->save();
                }
            }
            

            
            if($request->type && $request->type == 'transfer') { // transfer training to another date


                

                $result = RM::transferTraining($request->user_id, $request->date, $request->time);
                if($result == 1) {
                    History::user(Auth::user()->id, 'Перенос обучения', [
                        'data' => $request->all(),
                    ]);
                } else {
                    History::user(Auth::user()->id, 'Перенос обучения', [
                        'error' => $result,
                        'data' => $request->all(),
                    ]);
                }
                return  $result;
            }


            if($request->type && $request->type == 'report') {
                
                UserReport::create([
                    'user_id' => Auth::user()->id,
                    'title' => 'Отчет недельный',
                    'date' => date('Y-m-d'),
                    'text' => $request->text
                ]);

            }
            
 
            return 1;
        } else {
            return 0;
        }

        $history = TimetrackingHistory::create([
            'user_id' => $request->user_id,
            'author_id' => $user->id,
            'author' => $authorName,
            'date' => $date,
            'description' => $desc,
        ]);
        
    }

    public function setNotiReadAll(Request $request) {
        $notis = UserNotification::where('user_id', Auth::user()->id)->get();

        foreach($notis as $noti) {
            $noti->read_at = now();
            $noti->save();
        }

        return 1;
        
    }

    
    public function createPerson()
    {   
        // $user = Auth::user();
        // $rectuiting = ProfileGroup::find(48);
        // if($rectuiting) $users = json_decode($rectuiting->users);
        // if(in_array($user, ))

        if(!Auth::user()) return redirect('/');
        View::share('title', 'Новый сотрудник');
        View::share('menu', 'timetrackingusercreate');
        
        $roles = Auth::user()->roles ? Auth::user()->roles : [];
        
        if((array_key_exists('page22', $roles) && $roles['page22'] == 'on') || (array_key_exists('persons', $roles) && $roles['persons'] == 'on')) {
            return view('admin.users.create', $this->preparePersonInputs());
        }  else {
            return redirect('/');
        }
        
    }

    public function editPerson(Request $request)
    {   
        if(!Auth::user()) return redirect('/');
        View::share('title', 'Редактировать сотрудника');
        View::share('menu', 'timetrackingusercreate');

        $roles = Auth::user()->roles ? Auth::user()->roles : [];
        
        if((array_key_exists('page22', $roles) && $roles['page22'] == 'on') || (array_key_exists('persons', $roles) && $roles['persons'] == 'on')) {
            return view('admin.users.create', $this->preparePersonInputs($request->id));
        } else {
            return redirect('/');
        }
        
    }

    private function preparePersonInputs($id = 0)
    {
        $positions = Position::all();
        $groups = ProfileGroup::where('active', 1)->get();
        if($_SERVER['HTTP_HOST'] == env('ADMIN_DOMAIN', 'admin.u-marketing.org')) {
            $corpbooks = BookCategory::where('parent_cat_id', NULL)->where('is_deleted', 0)->get();
        } else {
            $corpbooks = '[]';
        }
        
        $programs = Program::orderBy('id', 'desc')->get();
        $workingDays = WorkingDay::all();
        $workingTimes = WorkingTime::all();
        $timezones = Setting::TIMEZONES;
        
        $arr = compact('positions', 'groups', 'timezones', 'programs', 'workingDays', 'workingTimes', 'corpbooks');

        if($id != 0) {
            $user = User::leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->withTrashed()
                ->where('users.id', $id)
                ->with(['zarplata', 'downloads', 'user_description'])
                ->first();
            
            $user->cards = Card::where('user_id', $user->id)->get();
            $user->delete_time = null;
            $head_in_groups = [];
       
            if($user) {
                if($user->trainee) {
                    $user->applied_at = $user->applied;
                } else {
                    $user->applied_at = $user->DATE_REGISTER;
                }

                if($user->is_trainee){
                    $arr['fire_causes'] = [
                        'Был на основной работе',
                        'Бросает трубку',
                        'Вышел (-ла) из группы',
                        'Забыл (-а), после обеда присутствует',
                        'Нашел(-а) другую работу',
                        'Не был на обучении / стажировке',
                        'Не выходит на связь',
                        'Не понравились условия оплаты труда',
                        'Не сдал экзамен',
                        'Не смог подключиться',
                        'Не хочет долго стажироваться',
                        'Не хочет работать 6 дней',
                        'Отказ от стажировки',
                        'Отсутствовал(а) более 3 дней',
                        'По техническим причинам',
                        'Пропал с обучения',
                        'Ребенок заболел, не сможет совмещать',
                        'Удалился (-ась), не актуально',
                    ];
                } else {
                    $arr['fire_causes'] = [
                        'Взял перерыв, позже возможно будет работать',
                        'Дисциплинарные нарушения',
                        'Дубликат, 2 учетки',
                        'Заказчик снял с проекта',
                        'Игнорирование предупреждений',
                        'Не справился с обязанностями',
                        'Конфликт с коллегами',
                        'Нашел(-а) другую работу',
                        'Неадекватная личность',
                        'Некому за ребенком присматривать',
                        'Не выходит на связь более 7 дней',
                        'Не успевает по учебе',
                        'Не устраивает график',
                        'Не устраивает ЗП',
                        'Не устраивает пункт в договоре',
                        'Оказалось что есть вторая работа',
                        'Переезд в другой город',
                        'Плохие рабочие показатели/не справился',
                        'По семейным обстоятельствам',
                        'По состоянию здоровья',
                        'По техническим причинам',
                        'Проект закрыт. Снят с линии',
                        'Решил(-а) работать оффлайн',
                        'Слишком большая нагрузка',
                    ];
                }



                $groups = $user->headInGroups();
            
                foreach($groups as $gr) {
                    array_push($head_in_groups, $gr);
                }

                $delete_plan = UserDeletePlan::where('user_id', $user->id)->orderBy('id', 'desc')->first();
                if($delete_plan) $user->delete_time = $delete_plan->delete_time;

                $ud = UserDescription::where('user_id', $user->id)->orderBy('id', 'desc')->first();
                if($ud){
                    $user->fire_cause = $ud->fire_cause;
                    $user->recruiter_comment = $ud->recruiter_comment;
                    $user->bitrix_id = $ud->bitrix_id;
                }

                $lead = Lead::where('user_id', $user->id)->first();
                $user->lead = $lead ? $lead : null;
                
                $seg = Segment::find($user->segment);
                $segment = $seg ? $seg->name : '';
                
                if($segment != '') {
                    $user->segment = $segment;
                }

                if($user->deactivate_date != null && $user->deactivate_date != '0000-00-00 00:00:00') {
                    $user->worked_with_us = round((Carbon::parse($user->deactivate_date)->timestamp - Carbon::parse($user->applied_at)->timestamp) / 3600 / 24) . ' дней';
                } else if(!$user->is_trainee && $user->deactivate_date == null) {
                    $user->worked_with_us = round((Carbon::now()->timestamp - Carbon::parse($user->applied_at)->timestamp) / 3600 / 24) . ' дней';
                } else {
                    $user->worked_with_us = 'Еще стажируется';
                }
                
                // humor

                if($user->id == 5)  $user->worked_with_us = 'Алеке 😁!';
                if($user->id == 18)  $user->worked_with_us = 'Не успел, а основал эту команду 😁!';
                if($user->id == 4444)  $user->worked_with_us = 'Успел, он же программист!';
                if($user->id == 157)  $user->worked_with_us = 'Весь КЦ на нем стоит 😁!';
                if($user->id == 84)  $user->worked_with_us = 'Да это же Моооля 😁!';

                $user->in_groups = $this->getPersonGroup($user->id);
                
                if($user->user_description) {
                    if($_SERVER['HTTP_HOST'] == env('ADMIN_DOMAIN', 'admin.u-marketing.org')) { 
                        $user->in_books  = BookCategory::whereIn('id', json_decode($user->user_description->books))->where('is_deleted', 0)->get();
                    } else {
                        $user->in_books  = '[]';
                    }
                    
                }
                
                $user->head_in_groups = $head_in_groups;
            }
            
            
            $user->adaptation_talks = AdaptationTalk::getTalks($user->id);

            $arr['user'] = $user;
        } 
        
        

        return $arr;
    }

    public function storePerson(Request $request) {
        
        /*==============================================================*/
        /********** Подготовка для приглашения на почту */
        /*==============================================================*/
        $original_password = User::generateRandomString();
        $salt = User::randString(8);
        $user_password = $salt . md5($salt . $original_password);
        $activate_key = User::generateRandomString(24);

        $data = [
            'activate_key' => $activate_key,
            'user_name' => $request['name'],
            'name' => $request['name'],
            'email' => $request['email'],
            'original_password' => $original_password,
        ];

        /*==============================================================*/
        /*******  Проверка существует ли пользователь в U-marketing.org */
        /*==============================================================*/
        
        
        $user = User::withTrashed()->where('email', $request['email'])->first();

        if ($user) { // Существует
            
            if ($user->deleted_at != null) {  // Ранее уволен
                $text = '<p>Нужно ввести другую почту, так как сотрудник c таким email ранее был уволен:</p>';
                $text .= '<table class="table" style="border-collapse: separate; margin-bottom: 15px;">';
                $text .= '<tr><td><b>Имя:</b></td><td>'.$user->name.'</td></tr>';
                $text .= '<tr><td><b>Фамилия:</b></td><td>'.$user->last_name.'</td></tr>';
                $text .= '<tr><td><b>Email:</b></td><td><a href="/timetracking/edit-person?id='. $user->id .'" target="_blank"> '. $user->email .'</a></td></tr>';
                $text .= '<tr><td><b>Дата увольнения:</b></td><td>'.Carbon::parse($user->deactivate_date)->setTimezone('Asia/Dacca').'</td></tr>';
                $text .= '</table>'; 
                return redirect()->to('/timetracking/create-person')->withInput()->withErrors($text);
            }
            
            if($user->UF_ADMIN == 1) { // Есть ли сотрудник
                $text = 'Нужно ввести другую почту, так как сотрудник c таким email уже существует! <br>' . $request['email'] .'<br><a href="/timetracking/edit-person?id=' . $user->id . '"   target="_blank">' . $user->last_name . ' ' . $user->name . '</a>';
                return redirect()->to('/timetracking/create-person')->withInput()->withErrors($text);
            } 
            // else {
            //     return redirect()->to('/timetracking/create-person')->withInput()->withErrors('Пользователь не является сотрудником, пожалуйста, обратитесь в тех.поддержку');
            // }

        }
        
        /*==============================================================*/
        /*******  Приглашение на почту  */
        /*==============================================================*/

        try { // если письмо с прилашением отправилось  
            $this->mail($request['email'], 'admin.mail.inviteadmin', 'Приглашение на портал', $data);
        } catch (Swift_TransportException $e) { // Если письмо по каким то причинам не отправилось
            return redirect()->to('/timetracking/create-person')->withInput()->withErrors('Возможно вы ввели не верный email или его не существует! <br><br> ' . $e->getMessage());
        }

        /*==============================================================*/
        /*******  Создание пользователя в U-marketing.org  */
        /*==============================================================*/

        //dd($request->all());

        //$user_password = 'opvLOUSe0200af69ff75617c3574485ba1da8f5d'; // 12345
        if($user) { // Если пользователь был ранее зарестрирован в cp.u-marketing.org
            $user->update([
                'name' => $request['name'],
                'last_name' => $request['last_name'],
                'DESCRIPTION' => $request['description'],
                'ACTIVE' => 'N',
                'PASSWORD' => $user_password,
                'DATE_REGISTER' => DB::raw('NOW()'),
                'UF_BALANCE' => 250,
                'position_id' => $request['position'],
                'UF_ADMIN' => 1,
                'roles' => $this->roles,
                'user_type' => $request->user_type,
                'timezone' => 6,
                'bitrhday' => $request['birthday'],
                'program_id' => (int)$request['program_type'],
                'working_day_id' => (int)$request['working_days'],
                'working_time_id' => (int)$request['working_times'],
                'PHONE' => $request['phone'],
                'phone_1' => $request['phone_1'],
                'phone_2' => $request['phone_2'],
                'phone_3' => $request['phone_3'],
                'phone_4' => $request['phone_4'],
                'full_time' => $request['full_time'],
                'work_start' => $request['work_start_time'],
                'work_end' => $request['work_start_end'],
                'currency' => $request['currency'],
                'weekdays' => $request['weekdays'],
            ]);    
        } else { // Не было никакого полльзователя с таким email
            $user = User::create([
                'email' => strtolower($request['email']),
                'name' => $request['name'],
                'last_name' => $request['last_name'],
                'DESCRIPTION' => $request['description'],
                'ACTIVE' => 'N',
                'PASSWORD' => $user_password,
                'DATE_REGISTER' => DB::raw('NOW()'),
                'UF_BALANCE' => 250,
                'position_id' => $request['position'],
                'UF_ADMIN' => 1,
                'roles' => $this->roles,
                'user_type' => $request->user_type,
                'timezone' => 6,
                'bitrhday' => $request['birthday'],
                'program_id' => (int)$request['program_type'],
                'working_day_id' => (int)$request['working_days'],
                'working_time_id' => (int)$request['working_times'],
                'PHONE' => $request['phone'],
                'phone_1' => $request['phone_1'],
                'phone_2' => $request['phone_2'],
                'phone_3' => $request['phone_3'],
                'phone_4' => $request['phone_4'],
                'full_time' => $request['full_time'],
                'work_start' => $request['work_start_time'],
                'work_end' => $request['work_start_end'],
                'currency' => $request['currency'],
                'weekdays' => $request['weekdays'],
            ]);
        }
        
        /*==============================================================*/
        /*******  Руковод или нет  */
        /*==============================================================*/
        
        if($request->head_group != 0 && $request->position == 45) {
            $hg = ProfileGroup::find($request->head_group);
            if($hg) {
                $heads = json_decode($hg->head_id);
                array_push($heads, $user->id);
                $hg->head_id = json_encode($heads);
                $hg->save();
            }
        }

        /*==============================================================*/
        /*******  Стажер или нет  */
        /*==============================================================*/

        if($request->is_trainee == 'true')  {
            $is_trainee = 1;
            // $user->DATE_REGISTER = null;
            // $user->save();
            $trainee = Trainee::where('user_id', $user->id)->first();
            if(!$trainee) Trainee::create(['user_id' => $user->id]);

            UserDescription::make([
                'user_id' => $user->id,
                'is_trainee' => $is_trainee,
            ]);

            $daytype = DayType::create([
                'user_id' => $user->id,
                'type' => 5, // Стажировка
                'email' => 'x',
                'date' => date('Y-m-d'),
                'admin_id' => Auth::user()->id,
            ]);
        } else {
            $is_trainee = 0;

            $whatsapp = new IC();
            $wphone = Phone::normalize($user->PHONE);
            $invite_link = 'https://infinitys.bitrix24.kz/?secret=bbqdx89w';
            //$whatsapp->send_msg($wphone, 'Ваша ссылка для регистрации в портале Битрикс24: %0a'. $invite_link . '.  %0a%0aВойти в учет времени: https://admin.u-marketing.org/login. %0aЛогин: ' . $user->email . ' %0aПароль: 12345.%0a%0a *Важно*: Если не можете через некоторое время войти в учет времени, попробуйте войти через e-mail, с которым зарегистрировались в Битрикс.');
            
            UserDescription::make([
                'user_id' => $user->id,
                'is_trainee' => $is_trainee,
                'applied' =>  DB::raw('NOW()'),
            ]);

            TimetrackingHistory::create([
                'author_id' => Auth::user()->id,
                'author' => Auth::user()->name.' '.Auth::user()->last_name,
                'user_id' => $user->id,
                'description' => 'Принятие на работу без стажировки',
                'date' => date('Y-m-d')
            ]);
        }

        

        /*==============================================================*/
        /*******  Сохранение доп телефонов для пользователя  */
        /*==============================================================*/

        if ($request->has('contacts') && isset($request->contacts['phone'])) {
            foreach ($request->contacts['phone'] as $phone) {
                $user->profileContacts()->save(
                    UserContact::create([
                        'user_id' => 0,
                        'type' => 'phone',
                        'name' => $phone['name'],
                        'value' => $phone['value'],
                    ])
                );
            }
        }

        /*==============================================================*/
        /********** Добавление дополнительных карт  */
        /*==============================================================*/ 
        
        if ($request->has('cards') && count($request->cards) > 0) {
            foreach ($request->cards as $card) {
               Card::create([
                'user_id' => $user->id,
                'bank' => $card['bank'],
                'country'=> $card['country'],
                'cardholder'=> $card['cardholder'],
                'phone' => $card['phone'],
                'number'=> $card['number'],
               ]); 
            }
        }


        /*==============================================================*/
        /*******  Создание пользователя в Callibro.org */
        /*==============================================================*/


        if($_SERVER['HTTP_HOST'] == env('ADMIN_DOMAIN', 'admin.u-marketing.org')) {
            $account = Account::where('email', $request['email'])->first();
            if (!$account) {
                $account = Account::create([
                    'password' => User::randString(16),
                    'owner_uid' => 5,
                    'name' => $request['name'],
                    'surname' => $request['last_name'],
                    'email' => strtolower($request['email']),
                    'status' => Account::ACTIVE_STATUS,
                    'role' => [Account::OPERATOR],
                    'activate_key' => $activate_key,
                ]);
            }

            $agent_name = $account->id . '@voip.cfpsa.ru';
            $agent = Agent::where('name', $agent_name)->first();
            if(!$agent) {
                $agent = Agent::create([
                    'name' => $agent_name,
                    'system' => 'single_box',
                    'type' => 'callback',
                    'contact' => Agent::CONTACT_PREFIX . $agent_name,
                    'status' => 'Logged Out',
                    'state' => 'Waiting'
                ]);
            }
            
            $directory = Directory::where('account', $account->id)->first();
            if(!$directory) {
                $directory = Directory::create([
                    'account' => $account->id,
                    'password' => $account->password,
                    'domain' => 'voip.cfpsa.ru',
                    'context' => 'voip.cfpsa.ru_context',
                    'provider' => '600',
                    'toll_allow' => '600',
                    'state' => 'active',
                ]);
            } else {
                $directory->password = $account->password;
                $directory->toll_allow = '600';
                $directory->provider = '600';
                $directory->domain = 'voip.cfpsa.ru';
                $directory->context = 'voip.cfpsa.ru_context';
                $directory->state = 'active';
                $directory->save();
            }
        }

        
        
        /*==============================================================*/
        /*******  Документы пользователя в U-marketing.org  */
        /*==============================================================*/

        if ($request->hasFile('file1')) {
            $file = $request->file('file1');
            $dog_okaz_usl = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move("static/profiles/" . $user->id . "/dog_okaz_usl", $dog_okaz_usl);
        }
        if ($request->hasFile('file2')) {
            $file = $request->file('file2');
            $sohr_kom_tainy = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move("static/profiles/" . $user->id . "/sohr_kom_tainy", $sohr_kom_tainy);
        }
        if ($request->hasFile('file3')) {
            $file = $request->file('file3');
            $dog_o_nekonk = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move("static/profiles/" . $user->id . "/dog_o_nekonk", $dog_o_nekonk);
        }
        if ($request->hasFile('file4')) {
            $file = $request->file('file4');
            $trud_dog = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move("static/profiles/" . $user->id . "/trud_dog", $trud_dog);
        }
        if ($request->hasFile('file5')) {
            $file = $request->file('file5');
            $ud_lich = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move("static/profiles/" . $user->id . "/ud_lich", $ud_lich);
        }
       
        if ($request->hasFile('file6')) {
            $file = $request->file('file6');
            $photo_file = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move("static/profiles/" . $user->id . "/photo", $photo_file);
            $photo = Photo::create([
                'user_id' => $user->id,
                'path' => isset($photo_file) ? $photo_file : null,
            ]);
        }
        if ($request->hasFile('file7')) {
            $file = $request->file('file7');
            $archive = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move("static/profiles/" . $user->id . "/archive", $archive);
        }

        

        $downloads = Downloads::create([
            'user_id' => $user->id,
            'ud_lich' => isset($ud_lich) ? $ud_lich : null,
            'dog_okaz_usl' => isset($dog_okaz_usl) ? $dog_okaz_usl : null,
            'sohr_kom_tainy' => isset($sohr_kom_tainy) ? $sohr_kom_tainy : null,
            'dog_o_nekonk' => isset($dog_o_nekonk) ? $dog_o_nekonk : null,
            'trud_dog' => isset($trud_dog) ? $trud_dog : null,
            'archive' => isset($archive) ? $archive : null,
        ]);

        /*==============================================================*/
        /*******  Зачисление пользователя в группу */
        /*==============================================================*/
        
        $group = ProfileGroup::find($request['group']);
        
        if($group) {
            if ($group->users !== null) {
                $users_array = json_decode($group->users);
                $users_array[] = $user->id;
            } else {
                $users_array = [];
                $users_array[] = $user->id;
            }
            $group->users = json_encode($users_array);

            if($request->increment_provided == 'true' && $group)  { 
                $group->provided = $group->provided + 1; /*******  Увеличиваем принятых в группу */
            }
            $group->save();
        }
        
        
        /*==============================================================*/
        /*******  Сохранить зарплату сотрудника  */
        /*==============================================================*/

        if ($user->zarplata === null) {
            $zarplata = new Zarplata();
            $zarplata->user_id = $user->id;
            $zarplata->zarplata = $request->zarplata == 0 ? 70000 : $request->zarplata;
            $zarplata->kaspi = $request->kaspi;
            $zarplata->jysan = $request->jysan;
            $zarplata->card_kaspi = $request->card_kaspi;
            $zarplata->card_jysan = $request->card_jysan;
            $zarplata->kaspi_cardholder = $request->kaspi_cardholder;
            $zarplata->jysan_cardholder = $request->jysan_cardholder;
            $zarplata->card_number = $request->card_number;
            $zarplata->save();
        } else {
            $user->zarplata()->update([
                'zarplata' => $request->zarplata == 0 ? 70000 : $request->zarplata,
                'card_number' => $request->card_number,
                'kaspi' => $request->kaspi,
                'jysan' => $request->jysan,
                'card_kaspi' => $request->card_kaspi,
                'card_kaspi' => $request->card_kaspi,
                'kaspi_cardholder' => $request->kaspi_cardholder,
                'jysan_cardholder' => $request->jysan_cardholder,
            ]);
        }



        return redirect()->to('/timetracking/edit-person?id=' . $user->id);
    }

    public function updatePerson(Request $request) {
      
        /*==============================================================*/
        /********** Подготовка  */
        /********** Есть момент, что можно посмотреть любого пользователя (не сотрудника UF_ADMIN), не знаю баг или нет  */
        /*==============================================================*/

        //if(Auth::user()->id == 5) dd($request->all());
        $id = $request['id'];
        $user = User::with('zarplata')->where('id', $id)->withTrashed()->first();
        $photo = Photo::where('user_id', $id)->first();
        $downloads = Downloads::where('user_id', $id)->first();
      
        $zarplata = !is_null($user->zarplata) && !is_null($user->zarplata->zarplata) ? $user->zarplata->zarplata : 0;
        
        if (is_null($user->roles) || $user->roles == '') {
            $user->roles = $this->roles;
        }

        

        /*==============================================================*/
        /********** Проверка новой почты существует ли  */
        /*==============================================================*/  
        $oldUser = User::withTrashed()->where('email', $request['email'])->first();
      
        if ($oldUser && $request['email'] != $user->email) { // Существует
            
            if ($oldUser->deleted_at != null) {  // Ранее уволен
                $text = '<p>Нужно ввести другую почту, так как сотрудник c таким email ранее был уволен:</p>';
                $text .= '<table class="table" style="border-collapse: separate; margin-bottom: 15px;">';
                $text .= '<tr><td><b>Имя:</b></td><td>'.$oldUser->name.'</td></tr>';
                $text .= '<tr><td><b>Фамилия:</b></td><td>'.$oldUser->last_name.'</td></tr>';
                $text .= '<tr><td><b>Email:</b></td><td><a href="/timetracking/edit-person?id='. $oldUser->id .'" target="_blank"> '. $oldUser->email .'</a></td></tr>';
                $text .= '<tr><td><b>Дата увольнения:</b></td><td>'.Carbon::parse($oldUser->deactivate_date)->setTimezone('Asia/Dacca').'</td></tr>';
                $text .= '</table>'; 
                return redirect()->to('/timetracking/edit-person?id=' . $request['id'])->withInput()->withErrors($text);
            }
            
            if($oldUser->UF_ADMIN == 1) { // Есть ли сотрудник
                $text = 'Нужно ввести другую почту, так как сотрудник c таким email уже существует! <br>' . $request['email'] .'<br><a href="/timetracking/edit-person?id=' . $oldUser->id . '"   target="_blank">' . $oldUser->last_name . ' ' . $oldUser->name . '</a>';
                return redirect()->to('/timetracking/edit-person?id=' . $request['id'])->withInput()->withErrors($text);
            } else {
                return redirect()->to('/timetracking/edit-person?id=' . $request['id'])->withInput()->withErrors('Пользователь не является сотрудником, пожалуйста, обратитесь в тех.поддержку');
            }

            
            
        } else {
            // Если нет другого аккаунта с новым email, то меняем уже сущ аккаунт в калибро
            $old_account = Account::where('email', $user->id)->where('owner_uid', 5)->first();
            if ($old_account) {
                $old_account->email = strtolower($request['email']);
                $old_account->status = Account::ACTIVE_STATUS;
                $old_account->save();
            }
        }
        

        /*==============================================================*/
        /********** Редактирование user  */
        /*==============================================================*/     

        $user->UF_ADMIN = 1; // Доступ в админку
        $user->email = strtolower($request['email']);
        $user->name = $request['name'];
        $user->last_name = $request['last_name'];
        $user->PHONE = $request['phone'];
        $user->phone_1 = $request['phone_1'];
        $user->phone_2 = $request['phone_2'];
        $user->phone_3 = $request['phone_3'];
        $user->phone_4 = $request['phone_4'];
        $user->birthday = $request['birthday'];
        $user->full_time = $request['full_time'];
        $user->DESCRIPTION = $request['description'];
        $user->currency = $request['currency'];
        $user->ACTIVE = 'Y';
        $user->position_id = $request['position'];
        $user->user_type = $request['user_type'];
        $user->timezone = 6;
        $user->program_id = (int)$request['program_type'];
        $user->working_day_id = (int)$request['working_days'];
        $user->working_time_id = (int)$request['working_times'];
        $user->work_start = $request['work_start_time'];
        $user->work_end = $request['work_start_end'];
        $user->weekdays = $request['weekdays'];
        
        if($request->new_pwd != '') {
            $salt = User::randString(8);
            $user->PASSWORD = $salt . md5($salt . $request->new_pwd);
        } 

        $user->save();


        /**Adaptation talks */

        foreach ($request->adaptation_talks as $key => $talk) {
            
            $at = AdaptationTalk::where('user_id', $user->id)->where('day', $talk['day'])->first();
            if($at) {
                $at->inter_id = $talk['inter_id'];
                $at->text = $talk['text'];
                $at->date = $talk['date'];
                $at->save();
            } else {
                AdaptationTalk::create([
                    'inter_id' => $talk['inter_id'],
                    'text' => $talk['text'],
                    'day' => $talk['day'],
                    'date' => $talk['date'],
                    'user_id' => $user->id,
                ]);
            }
        }
        


        /**
         *  Битрикс ID профиля
         */

        $ud = UserDescription::where('user_id', $user->id)
            ->first();

        if($ud) {
            $ud->bitrix_id = $request->bitrix_id;

            // Headphones for Salary
            if($request->headphones_amount > 0) {
                $ud->headphones_amount = $request->headphones_amount;
                $ud->headphones_date = date('Y-m-d');
            } else {
                $ud->headphones_amount = 0;
                $ud->headphones_date = null;
            }
            
            $ud->save();
        }

        /*==============================================================*/
        /*******  Стажер или нет  */
        /*==============================================================*/
        if($request->is_trainee == 'false')  {
            $trainee = Trainee::whereNull('applied')->where('user_id', $request['id'])->first();
            
            if($trainee) {
                $trainee->applied = DB::raw('NOW()');
                $trainee->save();

                $whatsapp = new IC();
                $wphone = Phone::normalize($user->PHONE);
                $invite_link = 'https://infinitys.bitrix24.kz/?secret=bbqdx89w';
                //$whatsapp->send_msg($wphone, 'Ваша ссылка для регистрации в портале Битрикс24: %0a'. $invite_link . '.  %0a%0aВойти в учет времени: https://admin.u-marketing.org/login. %0aЛогин: ' . $user->email . ' %0aПароль: 12345.%0a%0a *Важно*: Если не можете через некоторое время войти в учет времени, попробуйте войти через e-mail, с которым зарегистрировались в Битрикс.');

                $lead = Lead::where('user_id', $user->id)->orderBy('id', 'desc')->first();
                if($lead  && $lead->deal_id != 0) {
                    $bitrix = new Bitrix();
                    
                    $bitrix->changeDeal($lead->deal_id, [
                        'STAGE_ID' => 'C4:WON' // не присутствовал на обучении
                    ]);
                }
            }

            $ud = UserDescription::where('is_trainee', 1)
                ->where('user_id', $user->id)
                ->first();

            if($ud) {
                $ud->is_trainee = 0;
                $ud->applied = DB::raw('NOW()');
                $ud->save();
            }

            TimetrackingHistory::create([
                'author_id' => Auth::user()->id,
                'author' => Auth::user()->name.' '.Auth::user()->last_name,
                'user_id' => $user->id,
                'description' => 'Принятие на работу стажера',
                'date' => date('Y-m-d')
            ]);
        }

        /**
         * Оплатите внешнему рекрутеру за нового сотрудника
         */
        //dd($request->all());

        if(in_array($user->segment, [7,8,9,10,11,12])) {

            $ud = UserDescription::where('is_trainee', 0)->where('user_id', $user->id)->first();
            
            if($ud) {
                $comment = '';
                if($request->recruiter_comment != '') {
                    $ud->recruiter_comment = $request->recruiter_comment;
                    $ud->save();
                    $comment = $request->recruiter_comment;
                }   
                
                $seg = Segment::find($user->segment);
                $segment = $seg ? $seg->name : '';

                $msg_fragment = '<a href="https://admin.u-marketing.org/timetracking/edit-person?id=';
                $msg_fragment .= $user->id .'">' . $user->last_name . ' ' . $user->name . '</a>';
                $msg_fragment .= '<br/>Дата принятия: ' . Carbon::parse($ud->applied)->format('d.m.Y');
                $msg_fragment .= '<br/>Сегмент: ' . $segment . '<br/>Примечание: '. $comment;

                $timestamp = now();
                $notification_receivers = NotificationTemplate::getReceivers(10);
        
                foreach($notification_receivers as $user_id) {
                    UserNotification::create([
                        'user_id' => $user_id,
                        'about_id' => 0,
                        'title' => 'Оплатите внешнему рекрутеру за нового сотрудника',
                        'group' => $timestamp,
                        'message' => $msg_fragment
                    ]);

                }
            }

            
        }   

        /*==============================================================*/
        /*******  Руковод или нет  */
        /*==============================================================*/
        
        
        if($request->position != 45) {

            $last_groups = $user->headInGroups();
            foreach($last_groups as $gr) {
            
                $gr_users = json_decode($gr->head_id);
                $gr_users = array_diff($gr_users, [$user->id]);
                $gr_users = array_values($gr_users);
                $gr->head_id = json_encode($gr_users);
                $gr->save();
                   
            }      
            // $last_groups = $user->headInGroups();
            // foreach($last_groups as $gr) {
            
            //     $gr_users = json_decode($gr->head_id);
            //     $gr_users = array_diff($gr_users, [$user->id]);
            //     $gr_users = array_values($gr_users);
            //     $gr->head_id = json_encode($gr_users);
            //     $gr->save();
                   
            // }

            // $hg = ProfileGroup::find($request->head_group);
            // if($hg) {
            //     $heads = json_decode($hg->head_id);
            //     array_push($heads, $user->id);
            //     $hg->head_id = json_encode($heads);
            //     $hg->save();
            // }
        }
        /*==============================================================*/
        /********** Добавление дополнительных телефонов  */
        /*==============================================================*/ 
        
        if ($request->has('contacts') && isset($request->contacts['phone'])) {
            $user->profileContacts()->delete(); // Удаляет что было
            foreach ($request->contacts['phone'] as $phone) {
                $user->profileContacts()->save(
                    UserContact::create([
                        'user_id' => 0,
                        'type' => 'phone',
                        'name' => $phone['name'],
                        'value' => $phone['value'],
                    ])
                );
            }
        }

        /*==============================================================*/
        /********** Добавление дополнительных карт  */
        /*==============================================================*/ 
        
        if ($request->has('cards') && count($request->cards) > 0) {
            $cards = Card::where('user_id', $user->id)->delete();
            foreach ($request->cards as $card) {
               Card::create([
                'user_id' => $user->id,
                'bank' => $card['bank'],
                'country'=> $card['country'],
                'cardholder'=> $card['cardholder'],
                'phone' => $card['phone'],
                'number'=> $card['number'],
               ]); 
            }
        }

        
        /*==============================================================*/
        /********** Документы пользователя  */
        /*==============================================================*/
        if ($request->hasFile('file1')) {
            $file = $request->file('file1');
            $dog_okaz_usl = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move("static/profiles/" . $user->id . "/dog_okaz_usl", $dog_okaz_usl);
        }
        if ($request->hasFile('file2')) {
            $file = $request->file('file2');
            $sohr_kom_tainy = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move("static/profiles/" . $user->id . "/sohr_kom_tainy", $sohr_kom_tainy);
        }
        if ($request->hasFile('file3')) {
            $file = $request->file('file3');
            $dog_o_nekonk = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move("static/profiles/" . $user->id . "/dog_o_nekonk", $dog_o_nekonk);
        }
        if ($request->hasFile('file4')) {
            $file = $request->file('file4');
            $trud_dog = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move("static/profiles/" . $user->id . "/trud_dog", $trud_dog);
        }
        if ($request->hasFile('file5')) {
            $file = $request->file('file5');
            $ud_lich = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move("static/profiles/" . $user->id . "/ud_lich", $ud_lich);
        }
        if ($request->hasFile('file7')) {
            $file = $request->file('file7');
            $archive = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move("static/profiles/" . $user->id . "/archive", $archive);
        }

        if ($request->hasFile('file6')) {
            $file = $request->file('file6');
            $photo_file = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move("static/profiles/" . $user->id . "/photo", $photo_file);
            if (is_null($photo)) {
                $photo = Photo::create([
                    'user_id' => $id,
                    'path' => isset($photo_file) ? $photo_file : null,
                ]);
            } else {
                if (isset($photo_file)) {
                    $photo->path = $photo_file;
                    $photo->save();
                }
            }

        }
        
        if ($downloads) {
            if (isset($ud_lich)) $downloads->ud_lich = $ud_lich;
            if (isset($dog_okaz_usl)) $downloads->dog_okaz_usl = $dog_okaz_usl;
            if (isset($sohr_kom_tainy)) $downloads->sohr_kom_tainy = $sohr_kom_tainy;
            if (isset($dog_o_nekonk)) $downloads->dog_o_nekonk = $dog_o_nekonk;
            if (isset($trud_dog)) $downloads->trud_dog = $trud_dog;
            if (isset($archive)) $downloads->archive = $archive;
            $downloads->save();
        } else {
            $downloads = Downloads::create([
                'user_id' => $id,
                'ud_lich' => isset($ud_lich) ? $ud_lich : null,
                'dog_okaz_usl' => isset($dog_okaz_usl) ? $dog_okaz_usl : null,
                'sohr_kom_tainy' => isset($sohr_kom_tainy) ? $sohr_kom_tainy : null,
                'dog_o_nekonk' => isset($dog_o_nekonk) ? $dog_o_nekonk : null,
                'trud_dog' => isset($trud_dog) ? $trud_dog : null,
                'archive' => isset($archive) ? $archive : null,
            ]);
        }

        /*==============================================================*/
        /********** Редактирование аккаунта в callibro.org */
        /*==============================================================*/

        if($_SERVER['HTTP_HOST'] == env('ADMIN_DOMAIN', 'admin.u-marketing.org')) {
            $account = Account::where('email', $request['email'])->first();
            if ($account) {
                $account->name = $request['name'];
                $account->surname = $request['last_name'];
                $account->status = Account::ACTIVE_STATUS;
                $account->email = strtolower($request['email']);
                $account->save();


                $agent_name = $account->id . '@voip.cfpsa.ru';
                $agent = Agent::where('name', $agent_name)->first();
                if(!$agent) {
                    $agent = Agent::create([
                        'name' => $agent_name,
                        'system' => 'single_box',
                        'type' => 'callback',
                        'contact' => Agent::CONTACT_PREFIX . $agent_name,
                        'status' => 'Logged Out',
                        'state' => 'Waiting'
                    ]);
                }
                
                $directory = Directory::where('account', $account->id)->first();
                if(!$directory) {
                    $directory = Directory::create([
                        'account' => $account->id,
                        'password' => $account->password,
                        'domain' => 'voip.cfpsa.ru',
                        'context' => 'voip.cfpsa.ru_context',
                        'provider' => '600',
                        'toll_allow' => '600',
                        'state' => 'active',
                    ]);
                } else {
                    $directory->password = $account->password;
                    $directory->toll_allow = '600';
                    $directory->provider = '600';
                    $directory->domain = 'voip.cfpsa.ru';
                    $directory->context = 'voip.cfpsa.ru_context';
                    $directory->state = 'active';
                    $directory->save();
                }
            }
        }
        
        
        /*==============================================================*/
        /********** Редактирование зарплаты */
        /*==============================================================*/

        if ($user->zarplata === null) {
            $zarplata = new Zarplata();
            $zarplata->user_id = $user->id;
            $zarplata->zarplata = $request->zarplata == 0 ? 70000 : $request->zarplata;
            $zarplata->kaspi = $request->kaspi;
            $zarplata->jysan = $request->jysan;
            $zarplata->card_kaspi = $request->card_kaspi;
            $zarplata->kaspi_cardholder = $request->kaspi_cardholder;
            $zarplata->card_jysan = $request->card_jysan;
            $zarplata->jysan_cardholder = $request->jysan_cardholder;
            $zarplata->card_number = intval(preg_replace('/\s+/', '', $request->card_number));
            $zarplata->save();
        } else {
            $user->zarplata()->update([
                'zarplata' => $request->zarplata == 0 ? 70000 : $request->zarplata,
                'card_number' => $request->card_number,
                'kaspi' => $request->kaspi,
                'jysan' => $request->jysan,
                'card_kaspi' => $request->card_kaspi,
                'card_jysan' => $request->card_jysan,
                'kaspi_cardholder' => $request->kaspi_cardholder,
                'jysan_cardholder' => $request->jysan_cardholder,
            ]);
        }


        //////////////////////
        /******************* */
        //////////////////////
        $_groups = [];

        $groups = ProfileGroup::where('active', 1)->get();

        foreach($groups as $group) {
            if($group->users == null) continue;
            $group_users = json_decode($group->users);
            
            if(in_array($user->id, $group_users)) {
                $group->show = false;
                array_push($_groups, $group->id);  
            }
        }

        if($request->increment_provided == 'true' && count($_groups) > 0)  { 

            $group = ProfileGroup::find($_groups[0]);
            if($group) {
                $group->provided = $group->provided + 1; /*******  Увеличиваем принятых в группу */
                $group->save(); 
            }
            
        }

         //////////////////////
        /******************* */
        //////////////////////
        
        
        return redirect()->to('/timetracking/edit-person?id=' . $user->id);

    }

    
    public function editPersonBook(Request $request) {

        $user_id = $request->user_id;
        $book_id = $request->book_id;

        $ud = UserDescription::where('user_id', $user_id)->first();

        if(is_null($ud)) $ud = UserDescription::create(['user_id' => $user_id]);

        $books = json_decode($ud->books, true);
        
        if($request['action'] == 'add') {

            array_push($books, $book_id); 
            $books = array_unique($books);
        }

        if($request['action'] == 'delete') {
            if (($key = array_search($book_id, $books)) !== false) {
                unset($books[$key]);
                $books = array_values($books);
            }
        }
        
        $ud->books = json_encode($books);
        $ud->save();

    } 
    
    public function editPersonGroup(Request $request) {
        dd('123');
        $group = ProfileGroup::find($request['group_id']);
        $users = json_decode($group->users);
 
      
        if($request['action'] == 'add') {
            array_push($users, $request['user_id']); 
            $users = array_unique($users);
            
        }

        if($request['action'] == 'delete') {
            if (($key = array_search($request['user_id'], $users)) !== false) {
                unset($users[$key]);
            }
        }

        $users = array_values($users);
        $group->users = json_encode($users);
        $group->save();
        
    } 

    public function setUserHeadInGroups(Request $request) {



        $group = ProfileGroup::find($request['group_id']);
        $users = json_decode($group->head_id);
 
        if($request['action'] == 'add') {
            array_push($users, $request['user_id']); 
            $users = array_unique($users);
        }

        if($request['action'] == 'delete') {
            if (($key = array_search($request['user_id'], $users)) !== false) {
                unset($users[$key]);
            }
        }

        $users = array_values($users);
        $group->head_id = json_encode($users);
        $group->save();
    }

    public function getPersonGroup(int $user_id) {
        
        $_groups = [];

        $groups = ProfileGroup::where('active', 1)->get();

        foreach($groups as $group) {
            if($group->users == null) {
                $group->users = '[]';
            }
            $group_users = json_decode($group->users);
            
            if(in_array($user_id, $group_users)) {
                array_push($_groups, $group);  
            }
        }
        
        return $_groups;
        
    }

    public function deleteUser(Request $request)
    {
        $user = User::where([
            'id' => $request->id,
            'UF_ADMIN' => 1,
        ])->first();
        
        
        // Есть заявление об увольнении
        if ($request->hasFile('file8')) { // Заявление об увольнении
            $file = $request->file('file8');
            $resignation = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move("static/profiles/" . $user->id . "/resignation", $resignation);

            $downloads = Downloads::where('user_id', $user->id)->first();
            if ($downloads) {
                $downloads->resignation = $resignation;
                $downloads->save();
            } else {
                $downloads = Downloads::create([
                    'user_id' => $user->id,
                    'ud_lich' => null,
                    'dog_okaz_usl' => null,
                    'sohr_kom_tainy' => null,
                    'dog_o_nekonk' => null,
                    'trud_dog' => null,
                    'archive' => null,
                    'resignation' => $resignation,
                ]);
            }
        }


        ///////  УВолить с отработкой или без 

        if($request->delay == 1) { // Удалить через 2 недели

            $delete_plan = UserDeletePlan::where('user_id', $request->id)->orderBy('id', 'desc')->first();

            if($delete_plan) $delete_plan->delete();

            $fire_date = Carbon::now()->addHours(24 * 14);

            UserDeletePlan::create([
                'user_id' => $user->id,
                'executed' => 0,
                'delete_time' => $fire_date,
            ]);
            
        } else { // Сразу удалить

            
            
            /////////// Удалить связанные уведомления 
            $notis = UserNotification::where('about_id', $user->id)->get();
            if($notis->count() > 0) {
                foreach($notis as $noti) {
                    $noti->read_at = now();
                    $noti->save();
                }
            }
            
            //////////////////////////////

            $trainee = UserDescription::where('is_trainee', 1)->where('user_id', $request->id)->first();

            if($trainee) {
                if($trainee->lead_id != 0 && $trainee->lead_id) {
                    $lead = Lead::where('lead_id', $trainee->lead_id)->orderBy('id', 'desc')->first();
                } else {
                    $lead = Lead::where('phone', $user->PHONE)->orderBy('id', 'desc')->first();
                }
                
                if($lead) {
                    $bitrix = new Bitrix();
                    $deal_id = $bitrix->findDeal($lead->lead_id, false);
                   
                    if($deal_id != 0) {
                        $bitrix->changeDeal($deal_id, [
                            'STAGE_ID' => 'C4:12' // не присутствовал на обучении
                        ]);
                    }
                    
                }
            }

            $delete_plan = UserDeletePlan::where('user_id', $user->id)->orderBy('id', 'desc')->first();
            if($delete_plan) $delete_plan->delete();
            
            $fire_date = now();
            User::deleteUser($request); 
        }
        
        // Причина увольенения
        $cause = $request->cause2 == '' ? $request->cause : $request->cause2; 
        $ud = UserDescription::where('user_id', $request->id)->first();

        if($ud) { 
            $ud->fire_cause = $cause;
            $ud->fire_date = $fire_date;
            $ud->save();
        } else {
            UserDescription::create([
                'user_id' => $request->id,
                'fire_cause' => $cause,
                'fire_date' => $fire_date
            ]);
        }

        View::share('title', 'Сотрудник уволен');
        View::share('menu', 'timetrackinguser');

        return view('admin.users.create', $this->preparePersonInputs($request->id));
    }

    public function recoverUser(Request $request)
    {
        if(!Auth::user()) return redirect('/');

        $user = User::withTrashed()->where('id', $request->id)->first();
        
        if ($user) {
            $user->deactivate_date = null;
            $user->restore();

            $bitrix = new Bitrix();
           
            $bitrixUser = $bitrix->searchUser($user->email);
            usleep(1000000); // 1 sec
            if($bitrixUser) $success = $bitrix->recoverUser($bitrixUser['id']);

            /*** Восстановить с битрикс */

            // $bitrix = new Bitrix();
            // $bitrixUser = $bitrix->searchUser($user->email);
            // $success = false;
            // if($bitrixUser) $success = $bitrix->recoverUser($bitrixUser['id']);
            // if($success) {
            //     // Восстановлен в битрикс
            // } else {
            //     // Не Восстановлен
            // }

         
        } 

        View::share('title', 'Сотрудник восстановлен');
        View::share('menu', 'timetrackinguser');

        $roles = Auth::user()->roles ? Auth::user()->roles : [];
        
        if((array_key_exists('page22', $roles) && $roles['page22'] == 'on') || (array_key_exists('persons', $roles) && $roles['persons'] == 'on')) {
            return view('admin.users.create', $this->preparePersonInputs($request->id));
        }  else {
            return redirect('/');
        }
    }

    protected function mail($to, $template, $subject, $data)
    {
        $from = [
            'address' => 'no-repl@u-marketing.org', // env('MAIL_FROM_ADDRESS', 'no-reply@u-marketing.org'),
            'name' => env('MAIL_FROM_NAME', 'U-Marketing'),
        ];

        $transport = (new Swift_SmtpTransport('smtp.mail.ru', '465'))
            ->setEncryption('ssl')
            //->setUsername(env('MAIL_FROM_ADDRESS', 'no-reply@u-marketing.org'))
            ->setUsername('no-repl@u-marketing.org')
            ->setPassword('Asd123102030!'); //env('MAIL_PASSWORD', 'Asd123102030!!'));

        $mailer = app(Mailer::class);
        $mailer->setSwiftMailer(new Swift_Mailer($transport));
        $mailer->to($to)->send(new Mailable($template, $subject, $data, $from));
    }

    public function corp_book_read(Request $request)
    {
        $user = Auth::user();
        $user->read_corp_book_at = now();
        $user->save();

        return ['code' => 200];
    }

}
