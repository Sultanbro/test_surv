<?php

namespace App\Http\Controllers\Admin;

use App\Classes\Analytics\Recruiting;
use App\Events\TrackGroupChangingEvent;
use App\Events\TrackUserFiredEvent;
use App\Exports\UserExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserProfileUpdateRequest;
use App\KnowBase;
use App\Models\QuartalBonus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Mail as Mailable;
use Illuminate\Mail\Mailer;
use App\Models\Analytics\UserStat;
use App\Models\Analytics\Activity;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Swift_Mailer;
use Swift_SmtpTransport;
use Swift_TransportException;
use Illuminate\Http\Request;
use App\Kpi;
use App\Salary;
use Carbon\Carbon;
use App\Models\Kpi\Bonus;
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
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Admin\History;
use App\UserReport;
use App\Models\User\NotificationTemplate;
use App\Models\User\Card;
use App\Classes\Helpers\Currency;
use App\Models\TestBonus;
use App\Models\Admin\EditedBonus;
use App\Models\Admin\EditedKpi;
use App\Classes\UserAnalytics;
use App\QualityRecordWeeklyStat;
use App\Http\Controllers\Admin\GroupAnalyticsController as GAController;
use App\Models\Analytics\IndividualKpi;
use App\Models\Analytics\TraineeReport;
use App\AdaptationTalk;
use App\Models\GroupUser;
use App\Service\Department\UserService;
use \App\Service\Admin\UserService as AdminUserService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Image;
use Session;


class UserController extends Controller
{
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * surv
     */
    public function surv(Request $request)
    {
        View::share('menu', 'timetrackinguser');
        return view('test');
    }

    /**
     * Kairat quarter
     */
    public function quarter(\DateTime $dateTime)
    {
        return (int) ceil($dateTime->format('n') / 3);
    }

    /**
     * Профиль пользователя.
     * @param UserProfileUpdateRequest $request
     * @return bool
     */
    public function profile(UserProfileUpdateRequest $request)
    {
        $user = User::find(auth()->id());

        $service = new AdminUserService($user);

        if (isset($request->email))
        {
            $service->updateEmail($request);
        }

        if (isset($request->currency))
        {
            $service->updateCurrency($request);
        }

        if (isset($request->password))
        {
            $service->changePassword($request);
        }

        return true;
    }

    public function getProfile(Request $request)
    {
        $user = User::find(auth()->id() ?? 5);

        $currency_rate = in_array($user->currency, array_keys(Currency::rates())) ? (float)Currency::rates()[$user->currency] : 0.0000001;

        $positions = Position::all();
        $photo     = Photo::where('user_id', $user->id)->first();
        $downloads = Downloads::where('user_id', $user->id)->first();
        $user_position = Position::find($user->position_id);

        /*** Группы пользователя */
        $groups = '';
        $gs = $user->inGroups();

        foreach($gs as $group) {
            $groups .= '<div>' . $group['name'] . '</div>';
        }

        /*** Текущая книга для прочтения */
        $book = null;

        /* recruiter */


        $rg_users = [];
        if(tenant('id') == 'bp') {
            $rec_group = ProfileGroup::query()->findOrFail(Recruiting::GROUP_ID);
            $rg_users  = collect((new UserService)->getEmployees($rec_group->id, now()->toDateString()))->pluck('id')->toArray();
        }

        $recruiter_stats = json_encode([]);
        $recruiter_records = json_encode([]);

        // workdays recruiter

        $ignore = $user->working_day_id == 1 ? [6,0] : [0];
        $workdays = workdays(date('Y'), date('m'), $ignore);
        $wd = $user->workdays_from_applied(date('Y-m-d'), $user->working_day_id == 1 ? 5 : 6);
        if($wd != 0) $workdays = $wd;

        // another code

        if(in_array($user->id, $rg_users)) {
            $is_recruiter = true;
            $recruiter_stats = json_encode(RecruiterStat::tables(date('Y-m-d')));

            $asi  = AnalyticsSettingsIndividually::whereYear('date', date('Y'))
                ->whereMonth('date', date('m'))
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


        /**
         * костыль Корп универ должен видеть эту таблицу
         * TraineeReport::getBlocks
         */

        $corpUni = tenant('id') == 'bp'
            ? GroupUser::where('user_id', $user->id)
                ->where('status', 'active')
                ->where('group_id', 96)
                ->first()
            : null;

        /**
         * fetch TraineeReport::getBlocks
         * оценки руководителей
         */
        if($corpUni) {
            $head_in_groups = [1];
            $trainee_report = TraineeReport::getBlocks(date('Y-m-d'));

        }

        /**
         * checktime for trainees
         */
        // foreach($head_in_groups as $group) {
        //     if(Carbon::parse($group->checktime)->timestamp - time() >= 0) {
        //         $group->checktime = Carbon::parse($group->checktime)->setTimezone('Asia/Almaty');
        //     } else {
        //         $group->checktime = null;
        //     }
        // }

        // month for js

        $month = [
            'daysInMonth' => Carbon::now()->daysInMonth,
            'currentMonth' => Carbon::now()->format('F')
        ];

        /**
         * recruiter stats
         */
        $recruiter_stats_rates = [];

        for ($i = 1; $i <= Carbon::now()->daysInMonth; $i++) {
            $rec = new RM();
            $value = $rec->getOnlineRates(Carbon::now()->day($i)->format('Y-m-d'));
            $recruiter_stats_rates[$i] = $value;
        }
        $recruiter_stats_rates = json_encode($recruiter_stats_rates);

        /**
         * zarplata
         */
        $zarplata = Zarplata::where('user_id', $user->id)->first();

        $oklad = 0;
        if($zarplata) $oklad = $zarplata->zarplata;
        $oklad = round($oklad * $currency_rate, 0);
        $oklad = number_format($oklad, 0, '.', ' ');

        // arc
        $activities = '[]';
        $quality = [];
        if(count($gs) > 0) {
            $request->group_id = $gs[0]->id;
            $_activities = Activity::where('group_id', $gs[0]->id)->first();

            $activities = UserStat::activities($gs[0]->id , date('Y-m-d'));
            $activities = json_encode($activities);

            $users_ids = (new UserService)->getEmployees($gs[0]->id, date('Y-m-d'));

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


        return view('admin.timetracking', compact(
            'user',
            'oklad',
            'positions',
            'user_position',
            'photo',
            'downloads',
            'groups',
            'book',
            'is_recruiter',
            'indicators',
            'month',
            'recruiter_stats',
            'recruiter_stats_rates',
            'recruiter_records',
            'head_in_groups'
        ))->with([
            'answers' => UserExperience::getAnswers($user->id),
            'position_desc' => $position_desc,
            'groups_pt' => $gs,
            'show_payment_terms' => $show_payment_terms,
            'blocks_number' => $blocks_number,
            'activities' => $activities,
            'quality' => $quality,
            'trainee_report' => $trainee_report,
            'courses' => $user->getActiveCourses(),
            'workdays' => $workdays
        ]);
    }

    /**
     * recruiting temp
     */
    public function recruiting_temp()
    {
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

            $x_count = \DB::table('users')
                ->whereNull('deleted_at')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('ud.is_trainee', 0)
                ->get()
                ->count();

            $indicators['info']['working'] = $x_count;

            $training_users = Trainee::whereNull('applied')->pluck('user_id')->toArray();
            $t_users = \DB::table('users')
                ->whereNull('deleted_at')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('ud.is_trainee', 1)
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

    /**
     * change pass
     */
    public function changePassword(Request $request)
    {

        $user = Auth::user();

        if(!empty($request->password) && $request->password == $request->repassword) {
            $salt = User::randString(8);
            $password = $salt.md5($salt.$request->password);
            $user->password = $password;
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

        $groups = ProfileGroup::where('active', 1)->get();
  
        if (isset($request['filter']) && $request['filter'] == 'all') {

            //$users = User::withTrashed()->whereIn('email', $array_accounts_email);
            $users = \DB::table('users')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id');

            if($request['job'] != 0){
                $users = \DB::table('users')
                ->where('position_id',$request['job'])
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id');
            }            

            if ($request['start_date']) $users = $users->whereDate('created_at', '>=', $request['start_date']);
            if ($request['end_date']) $users = $users->whereDate('created_at', '<=', $request['end_date']);
            if ($request['start_date_deactivate']) $users = $users->whereDate('deleted_at', '>=', $request['start_date_deactivate']);
            if ($request['end_date_deactivate']) $users = $users->whereDate('deleted_at', '<=', $request['end_date_deactivate']);

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
            $users = \DB::table('users')
                ->whereNotNull('deleted_at')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('is_trainee', 0);

            if($request['job'] != 0){
                $users = \DB::table('users')
                ->where('position_id',$request['job'])
                ->whereNotNull('deleted_at')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('is_trainee', 0);
            } 
            
            if ($request['start_date_deactivate']) $users = $users->whereDate('deleted_at', '>=', $request['start_date_deactivate']);
            if ($request['end_date_deactivate']) $users = $users->whereDate('deleted_at', '<=', $request['end_date_deactivate']);
            if ($request['segment'] != 0) $users = $users->where('segment', $request['segment']);
            // $trainees = Trainee::whereNull('applied')->get()->pluck('user_id')->toArray();
            // $users = $users->whereNotIn('users.id', $trainees);
            
        } elseif(isset($request['filter']) && $request['filter'] == 'nonfilled') {

            $users_1 = \DB::table('users')
                ->whereNull('deleted_at')
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
            
            $users = \DB::table('users')
                ->whereNull('deleted_at')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('is_trainee', 0)
                ->where(function($query){
                    $query->whereNull('users.position_id')
                            ->orWhereNull('users.phone')
                            ->orWhereNull('users.birthday')
                            ->orWhereNull('users.working_day_id')
                            ->orWhereNull('users.working_time_id');
                })
                ->orWhere('is_trainee', 0)
                ->whereIn('users.id', array_values($users_1));
            

        } elseif(isset($request['filter']) && $request['filter'] == 'trainees') {

            $users = \DB::table('users')
                ->whereNull('deleted_at')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('is_trainee', 1)
                ->whereNull('ud.fire_date');

            if($request['job'] != 0){
                $users = \DB::table('users')
                ->where('position_id',$request['job'])
                ->whereNull('deleted_at')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('is_trainee', 1)
                ->whereNull('ud.fire_date');
            }
            
            if ($request['start_date']) $users = $users->whereDate('created_at', '>=', $request['start_date']);
            if ($request['end_date']) $users = $users->whereDate('created_at', '<=', $request['end_date']);
            if ($request['start_date_deactivate']) $users = $users->whereDate('deleted_at', '>=', $request['start_date_deactivate']);
            if ($request['end_date_deactivate']) $users = $users->whereDate('deleted_at', '<=', $request['end_date_deactivate']);
            
            
        } else {

            $users = \DB::table('users')
                ->whereNull('deleted_at')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('is_trainee', 0);

            if($request['job'] != 0){
                $users = \DB::table('users')
                ->where('position_id',$request['job'])
                ->whereNull('deleted_at')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('is_trainee', 0);
            }
            
           // $trainees = Trainee::whereNull('applied')->get()->pluck('user_id')->toArray();
            if ($request['start_date']) $users = $users->whereDate('created_at', '>=', $request['start_date']);
            if ($request['end_date']) $users = $users->whereDate('created_at', '<=', $request['end_date']);
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
            'users.created_at',
            'users.deleted_at',
            'users.last_group',
            'users.position_id',
            'users.phone',
            'users.birthday',
            'users.description',
            'users.working_day_id',
            'users.working_time_id',
            'users.work_start',
            'users.work_end',
            'users.program_id',
            'ud.fire_cause',
            'ud.applied',
        ]);

        foreach ($users as $key => $user) {

            $_user = User::withTrashed()->find($user->id);

            $user->groups = $_user ? $_user->inGroups()->pluck('id')->toArray() : [];
            
            if(is_null($user->deleted_at) || $user->deleted_at == '0000-00-00 00:00:00') {
                $user->deleted_at = '';
            } else {
                $user->deleted_at = Carbon::parse($user->deleted_at)->addHours(6)->format('Y-m-d H:i:s');
                if($user->deleted_at == '30.11.-0001 00:00:00') {
                    $user->deleted_at = '';
                } 
            }

            
            
            // if(is_null($user->has_trainee)) {
            //     $user->applied = $user->created_at;
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
            

            $user->created_at = Carbon::parse($user->created_at)->addHours(6)->format('Y-m-d H:i:s');
    
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
            $export = new UserExport($users, $groups);
            $title = 'Сотрудники: ' . date('Y-m-d') . '.xlsx';
            return Excel::download($export, $title);
        }   
            
        
            
        $users = $users->values();
        


        ////////////////////

        return [
            'users' => $users,
            'can_login_users' => [5,18,1],
            'auth_token' => Auth::user()->remember_token,
            'currentUser' => Auth::user()->id,
            'segments' => Segment::pluck('name', 'id'),
            'groups' => [0 => 'Выберите группу'] + $groups,
            'start_date' => Carbon::now()->startOfMonth()->format('Y-m-d'),
            'end_date' => Carbon::now()->endOfMonth()->format('Y-m-d'),
        ];
    }
    
    /**
     * setNotiRead
     */
    public function setNotiRead(Request $request)
    {

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
            'user_id'     => $request->user_id,
            'author_id'   => $user->id,
            'author'      => $authorName,
            'date'        => $date,
            'description' => $desc,
        ]);
        
    }

    /**
     * setNotiReadAll
     */
    public function setNotiReadAll(Request $request)
    {
        $notis = UserNotification::where('user_id', Auth::user()->id)->get();

        foreach($notis as $noti) {
            $noti->read_at = now();
            $noti->save();
        }

        return 1;
        
    }

    /**
     * createPerson
     */
    public function createPerson()
    {   
        // $user = Auth::user();
        // $rectuiting = ProfileGroup::find(48);
        // if($rectuiting) $users = json_decode($rectuiting->users);
        // if(in_array($user, ))

        if(!Auth::user()) return redirect('/');

        View::share('title', 'Новый сотрудник');
        View::share('menu', 'timetrackingusercreate');
        
        if(!auth()->user()->can('users_view')) {
            return redirect('/');
        }

        return view('admin.users.create', $this->preparePersonInputs());
        
    }

    /**
     * editPerson
     *
     * @param Request $request
     */
    public function editPerson(Request $request,$type = null)
    {





        if(!Auth::user()) return redirect('/');
        View::share('title', 'Редактировать сотрудника');
        View::share('menu', 'timetrackingusercreate');

        
        if(!auth()->user()->can('users_view')) {
            return redirect('/');
        }


        return view('admin.users.create',
                 $this->preparePersonInputs($request->id)

        );
        
    }

    /**
     * prepare user variables to settings page
     */
    private function preparePersonInputs($id = 0)
    {
        $positions = Position::all();
        $groups    = ProfileGroup::where('active', 1)->get();
        $corpbooks = [];

        $knowbase_models = DB::table('knowbase_model')
            ->where('model_id', auth()->user()->getAuthIdentifier())
            ->get()
            ->toArray();

        if (!empty($knowbase_models)){

            foreach ($knowbase_models as $k => $knowbase_model){
                $knowbase_query[] = KnowBase::where('id',$knowbase_model->book_id)->get()->toArray();
            }

            foreach ($knowbase_query as $corpbook){
                $corpbooks[] = array_shift($corpbook);

            }

        }




        $programs = Program::orderBy('id', 'desc')->get();
        $workingDays = WorkingDay::all();
        $workingTimes = WorkingTime::all();
        $timezones = Setting::TIMEZONES;
        
        $arr = compact('positions', 'groups', 'timezones', 'programs', 'workingDays', 'workingTimes', 'corpbooks');

        if($id != 0) {
            $user = User::withTrashed()
                ->where('id', $id)
                ->with(['zarplata', 'downloads', 'user_description'])
                ->first();
            
            if($user->weekdays == '' || $user->weekdays == null) {
                $user->weekdays = '0000000';
                $user->save();
            }    

            $user->cards = Card::where('user_id', $user->id)->get();
            $user->delete_time = null;
            $head_in_groups = [];
       
            if($user) {
                if($user->trainee) {
                    $user->applied_at = $user->applied;
                } else {
                    $user->applied_at = $user->created_at;
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

                
                if($user->user_description){
                    $user->fire_cause = $user->user_description->fire_cause;
                    $user->recruiter_comment = $user->user_description->recruiter_comment;
                    $user->bitrix_id = $user->user_description->bitrix_id;
                }

                $lead = Lead::where('user_id', $user->id)->first();
                $user->lead = $lead ? $lead : null;
                
                $seg = Segment::find($user->segment);
                $segment = $seg ? $seg->name : '';
                
                if($segment != '') {
                    $user->segment = $segment;
                }

                if($user->deleted_at != null && $user->deleted_at != '0000-00-00 00:00:00') {
                    $user->worked_with_us = round((Carbon::parse($user->deleted_at)->timestamp - Carbon::parse($user->applied_at)->timestamp) / 3600 / 24) . ' дней';
                } else if(!$user->is_trainee && $user->deleted_at == null) {
                    $user->worked_with_us = round((Carbon::now()->timestamp - Carbon::parse($user->created_at)->timestamp) / 3600 / 24) . ' дней';
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
                    $user->in_books  = '[]';
                }
                
                $user->head_in_groups = $head_in_groups;
            }
            
            
            $user->adaptation_talks = AdaptationTalk::getTalks($user->id);

            $arr['user'] = $user;
        }






        return $arr;
    }

    /**
     * storePerson
     *
     * @param Request $request
     */
    public function storePerson(Request $request)
    {



        if(!auth()->user()->can('users_view')) {
            return redirect('/');
        }

        /*==============================================================*/
        /********** Подготовка для приглашения на почту */
        /*==============================================================*/
        $original_password = User::generateRandomString();
        $user_password = \Hash::make($original_password);

        $data = [
            'user_name' => $request['name'],
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => $original_password,
            'subdomain' => tenant('id')
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
                $text .= '<tr><td><b>Дата увольнения:</b></td><td>'.Carbon::parse($user->deleted_at)->setTimezone('Asia/Dacca').'</td></tr>';
                $text .= '</table>'; 
                return redirect()->to('/timetracking/create-person')->withInput()->withErrors($text);
            }
            
            $text = 'Нужно ввести другую почту, так как сотрудник c таким email уже существует! <br>' . $request['email'] .'<br><a href="/timetracking/edit-person?id=' . $user->id . '"   target="_blank">' . $user->last_name . ' ' . $user->name . '</a>';
            return redirect()->to('/timetracking/create-person')->withInput()->withErrors($text);
            // else {
            //     return redirect()->to('/timetracking/create-person')->withInput()->withErrors('Пользователь не является сотрудником, пожалуйста, обратитесь в тех.поддержку');
            // }

        }
        
        /*==============================================================*/
        /*******  Приглашение на почту  */
        /*==============================================================*/

      
       
        try { // если письмо с прилашением отправилось

      
            \Mail::to($request['email'])->send(new \App\Mail\SendInvitation($data));
         
        } catch (Throwable $e) { // Если письмо по каким то причинам не отправилось
      
            return redirect()->to('/timetracking/create-person')->withInput()->withErrors('Возможно вы ввели не верный email или его не существует! <br><br> ' . $e->getMessage());
        }
    
      

        /*==============================================================*/
        /*******  Создание пользователя в U-marketing.org  */
        /*==============================================================*/


        


        if($user) { // Если пользователь был ранее зарестрирован в cp.u-marketing.org
            $user->update([
                'name' => $request['name'],
                'last_name' => $request['last_name'],
                'description' => $request['description'],
                'password' => $user_password,
                'created_at' => DB::raw('NOW()'),
                'position_id' => $request['position'],
                'user_type' => $request->user_type,
                'timezone' => 6,
                'bitrhday' => $request['birthday'],
                'program_id' => (int)$request['program_type'],
                'working_day_id' => (int)$request['working_days'],
                'working_time_id' => (int)$request['working_times'],
                'phone' => $request['phone'],
                'full_time' => $request['full_time'],
                'work_start' => $request['work_start_time'],
                'work_end' => $request['work_start_end'],
                'currency' => $request['currency'] ?? 'kzt',
                'weekdays' => $request['weekdays'],
                'working_country' => $request['selectedCityInput'],
                'working_city' => $request['working_city'],
            ]);    
        } else { // Не было никакого полльзователя с таким email




            $user = User::create([
                'email' => strtolower($request['email']),
                'name' => $request['name'],
                'last_name' => $request['last_name'],
                'description' => $request['description'],
                'password' => $user_password,
                'position_id' => $request['position'],
                'user_type' => $request->user_type,
                'timezone' => 6,
                'bitrhday' => $request['birthday'],
                'program_id' => (int)$request['program_type'],
                'working_day_id' => (int)$request['working_days'],
                'working_time_id' => (int)$request['working_times'],
                'phone' => $request['phone'],
                'full_time' => $request['full_time'],
                'work_start' => $request['work_start_time'],
                'work_end' => $request['work_start_end'],
                'currency' => $request['currency'] ?? 'kzt',
                'weekdays' => $request['weekdays'],
                'working_country' =>$request['selectedCityInput'],
                'working_city' =>$request['working_city'],
                'role_id' => 1,
                'is_admin' => 0,
                'img_url' => $request['file_name']
            ]);
            if($request['program_type'] == 2){
                Account::create([
                    'password' => $user_password,
                    'owner_uid' => 5,
                    'name' => $request['name'],
                    'surname' => $request['last_name'],
                    'email' => strtolower($request['email']),
                    'status' => Account::ACTIVE_STATUS,
                    'role' => [Account::OPERATOR],
                    'activate_key' => '',
                ]);
            }
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
            // $user->created_at = null;
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
            $wphone = Phone::normalize($user->phone);
            $invite_link = 'https://infinitys.bitrix24.kz/?secret=bbqdx89w';
            //$whatsapp->send_msg($wphone, 'Ваша ссылка для регистрации в портале Битрикс24: %0a'. $invite_link . '.  %0a%0aВойти в учет времени: https://bp.jobtron.org/login. %0aЛогин: ' . $user->email . ' %0aПароль: 12345.%0a%0a *Важно*: Если не можете через некоторое время войти в учет времени, попробуйте войти через e-mail, с которым зарегистрировались в Битрикс.');
            
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

    /**
     * Update user profile from settings
     *
     * @param Request $request
     */
    public function updatePerson(Request $request)
    {
        if(!auth()->user()->can('users_view')) {
            return redirect('/');
        }
        /*==============================================================*/
        /********** Подготовка  */
        /********** Есть момент, что можно посмотреть любого пользователя (не сотрудника ), не знаю баг или нет  */
        /*==============================================================*/

        //if(Auth::user()->id == 5) dd($request->all());
        $id = $request['id'];
        $user = User::with('zarplata')->where('id', $id)->withTrashed()->first();
        $photo = Photo::where('user_id', $id)->first();
        $downloads = Downloads::where('user_id', $id)->first();
      
        $zarplata = !is_null($user->zarplata) && !is_null($user->zarplata->zarplata) ? $user->zarplata->zarplata : 0;
        

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
                $text .= '<tr><td><b>Дата увольнения:</b></td><td>'.Carbon::parse($oldUser->deleted_at)->setTimezone('Asia/Dacca').'</td></tr>';
                $text .= '</table>'; 
                return redirect()->to('/timetracking/edit-person?id=' . $request['id'])->withInput()->withErrors($text);
            }
            
            
                $text = 'Нужно ввести другую почту, так как сотрудник c таким email уже существует! <br>' . $request['email'] .'<br><a href="/timetracking/edit-person?id=' . $oldUser->id . '"   target="_blank">' . $oldUser->last_name . ' ' . $oldUser->name . '</a>';
                return redirect()->to('/timetracking/edit-person?id=' . $request['id'])->withInput()->withErrors($text);
          

            
            
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

        if (isset($request['selectedCityInput']) && !empty($request['selectedCityInput']) ) {

            if (auth()->user()->working_city === $request['working_city']){
                $country = $request['selectedCityInput'];
                $explodeCountry = explode(' ',$country);
                foreach ($explodeCountry as $country){
                    $searchCountry = DB::table('coordinates')->where('city',$country)->get()->toArray();
                }
                if (isset($searchCountry[0]->id) && !empty($searchCountry)){
                    $request['working_city'] = $searchCountry[0]->id;
                }else{
                    $request['working_city'] = null;
                    $request['selectedCityInput'] = null;
                }
            }
        }else{
            $request['working_city'] = null;
            $request['selectedCityInput'] = null;
        }

        $user->email = strtolower($request['email']);
        $user->name = $request['name'];
        $user->last_name = $request['last_name'];
        $user->phone = $request['phone'];
        $user->phone_1 = $request['phone_1'];
        $user->phone_2 = $request['phone_2'];
        $user->phone_3 = $request['phone_3'];
        $user->phone_4 = $request['phone_4'];
        $user->birthday = $request['birthday'];
        $user->full_time = $request['full_time'];
        $user->description = $request['description'];
        $user->currency = $request['currency'] ?? 'kzt';
        $user->position_id = $request['position'];
        $user->user_type = $request['user_type'];
        $user->timezone = 6;
        $user->program_id = (int)$request['program_type'];
        $user->working_day_id = (int)$request['working_days'];
        $user->working_time_id = (int)$request['working_times'];
        $user->work_start = $request['work_start_time'];
        $user->work_end = $request['work_start_end'];
        $user->weekdays = $request['weekdays'];
        $user->working_country = $request['selectedCityInput'];
        $user->working_city = $request['working_city'];
        $user->headphones_sum = $request['headphones_amount'];


        if($request->new_pwd != '') {
            $user->password = \Hash::make($request->new_pwd);
        } 

        $user->save();


        /**
         * Adaptation talks
         */
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
                $wphone = Phone::normalize($user->phone);
                $invite_link = 'https://infinitys.bitrix24.kz/?secret=bbqdx89w';
                //$whatsapp->send_msg($wphone, 'Ваша ссылка для регистрации в портале Битрикс24: %0a'. $invite_link . '.  %0a%0aВойти в учет времени: https://bp.jobtron.org/login. %0aЛогин: ' . $user->email . ' %0aПароль: 12345.%0a%0a *Важно*: Если не можете через некоторое время войти в учет времени, попробуйте войти через e-mail, с которым зарегистрировались в Битрикс.');

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

                $msg_fragment = '<a href="https://bp.jobtron.org/timetracking/edit-person?id=';
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
        if($request->position == 45) {

            $last_groups = $user->headInGroups();
            foreach($last_groups as $gr) {
            
                $gr_users = json_decode($gr->head_id);
                $gr_users = array_diff($gr_users, [$user->id]);
                $gr_users = array_values($gr_users);
                $gr->head_id = json_encode($gr_users);
                $gr->save();
                   
            }
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
        /********** Редактирование зарплаты */
        /*==============================================================*/

        if ($user->zarplata === null) {
            $zarplata = new Zarplata();
            $zarplata->user_id = $user->id;
            $zarplata->zarplata =  $request->zarplata == 0 ? 70000 : $request->zarplata;
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
                'zarplata' => $request->zarplata,
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

    /**
     * editPersonBook
     *
     * @param Request $request
     */
    public function editPersonBook(Request $request)
    {

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

    /**
     * edit user groups in settings
     *
     * @param Request $request
     *
     * @throws \Exception
     */
    public function editPersonGroup(Request $request) {

        $group = ProfileGroup::find($request['group_id']);
        $exist = $group->users()->where([
            ['user_id', $request['user_id']],
            ['status', 'active']
        ])->whereNull('to')->exists();

        try {
            if($request['action'] == 'add' && !$exist) {
                $group->users()->attach($request['user_id'], [
                    'from' => Carbon::now()->toDateString()
                ]);
            }

            if($request['action'] == 'delete') {
                event(new TrackGroupChangingEvent($request['user_id'], $request['group_id']));
                $group->users()->where('user_id', $request['user_id'])->whereNull('to')->update([
                    'to' => Carbon::now()->toDateString(),
                    'status'     => 'drop'
                ]);
            }
        }catch (\Exception $exception) {
            throw new \Exception($exception);
        }
    }

    /**
     * set user in groups as head
     *
     * @return void
     */
    public function setUserHeadInGroups(Request $request) : void
    {
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

    /**
     * get user groups
     *
     * @param int $user_id
     *
     * @return array
     */
    public function getPersonGroup(int $user_id) : array
    {
        $groups =  GroupUser::where('user_id', $user_id)
            ->where('status', 'active')
            ->get()
            ->pluck('group_id')
            ->toArray();
        // $g= ProfileGroup::whereIn('id', array_values($groups))
        // ->select(['id', 'name'])
        // ->get()
        // ->toArray();

        return ProfileGroup::whereIn('id', array_values($groups))
        ->select(['id', 'name'])
        ->get()
        ->toArray();

        // return

        //     $_groups = [];

        // $groups = ProfileGroup::where('active', 1)->get();

        // foreach($groups as $group) {
        //     if($group->users == null) {
        //         $group->users = '[]';
        //     }
        //     $group_users = json_decode($group->users);

        //     if(in_array($user_id, $group_users)) {
        //         array_push($_groups, $group);
        //     }
        // }

        // return $_groups;
    }

    /**
     * Fire user
     */
    public function deleteUser(Request $request)
    {
        DB::transaction(function () use ($request){
            $user = User::where([
                'id' => $request->id,
            ])->first();

            event(new TrackUserFiredEvent($user));

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
                        $lead = Lead::where('phone', $user->phone)->orderBy('id', 'desc')->first();
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
        });

        View::share('title', 'Сотрудник уволен');
        View::share('menu', 'timetrackinguser');

        return view('admin.users.create', $this->preparePersonInputs($request->id));
    }

    /**
     * Restore user
     */
    public function recoverUser(Request $request)
    {
        if(!Auth::user()) return redirect('/');

        $user = User::withTrashed()->where('id', $request->id)->first();
        
        if ($user) {
            $user->deleted_at = null;
            $user->restore();

            $bitrix = new Bitrix();
           
            $bitrixUser = $bitrix->searchUser($user->email);
            usleep(1000000); // 1 sec
            if($bitrixUser) $success = $bitrix->recoverUser($bitrixUser['ID']);

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

      
        return view('admin.users.create', $this->preparePersonInputs($request->id));
    }

    /**
     * send mail Maybe not working
     */
    protected function mail($to, $template, $subject, $data)
    {
        $from = [
            'address' => 'no-reply@jobtron.org', // env('MAIL_FROM_ADDRESS', 'no-reply@u-marketing.org'),
            'name' => env('MAIL_FROM_NAME', 'Jobtron'),
        ];

        $transport = (new Swift_SmtpTransport('smtp.timeweb.ru', '465'))
            ->setEncryption('ssl')
            //->setUsername(env('MAIL_FROM_ADDRESS', 'no-reply@u-marketing.org'))
            ->setUsername('no-reply@jobtron.org')
            ->setPassword('FgzNZ7Cj!'); //env('MAIL_password', 'Asd123102030!!'));

        $mailer = app(Mailer::class);
        $mailer->setSwiftMailer(new Swift_Mailer($transport));
        $mailer->to($to)->send(new Mailable($template, $subject, $data, $from));
    }

    /**
     * corp_book_read
     */
    public function corp_book_read(Request $request)
    {
        $user = User::find(auth()->id());
        $user->read_corp_book_at = now();
        $user->notified_at = now();
        $user->save();

        return ['code' => 200];
    }

    /**
     * добавление карты и измение данный через профиль
     */
    public function editUserProfile(Request $request)
    {
        if (isset($request->cards) && !empty($request->cards)){
            Card::where('user_id',auth()->user()->getAuthIdentifier())->delete();
            foreach ($request->cards as $card) {
                Card::create([
                    'user_id' => auth()->user()->getAuthIdentifier(),
                    'bank' => $card['bank'],
                    'country'=> $card['country'],
                    'cardholder'=> $card['cardholder'],
                    'phone' => $card['phone'],
                    'number'=> $card['number'],
                ]);
            }
        }

        $user = User::find(auth()->user()->getAuthIdentifier());
        $user['name'] = $request['query']['name'];
        $user['birthday'] = $request['birthday'];
        if (isset($request->password) && !empty($request->password)){
            $user['password'] = Hash::make($request->password);
        }
        $user['last_name'] = $request['query']['last_name'];
        $user['working_country'] = $request['working_country'];
        $user['working_city'] = $request['working_city'];
        if ($user->save()){
            return response(['success'=>'1']);
        }




    }

    /**
     * загрузка аватарки через настроки ( в шаблоне blade ) Kairat
     */
    public function uploadPhoto(Request $request)
    {





        $data = $request["image"];


        $image_array_1 = explode(";", $data);



        $image_array_2 = explode(",", $image_array_1[1]);


        $data = base64_decode($image_array_2[1]);

        $imageName = time() . '.png';



        if (isset($request['user_id']) && $request['user_id'] != 'new_user') {
            $update_user = User::withTrashed()->find($request['user_id']);

            if (!empty($update_user->img_url)){
                $filename = "users_img/".$update_user->img_url;

                if (file_exists($filename)) {
                    unlink(public_path('users_img/'.$update_user->img_url));
//                    unlink("users_img/".$update_user->img_url);
                }

            }

            $update_user->img_url = $imageName;
            $update_user->save();

            file_put_contents("users_img/$imageName", $data);

            $img = '<img src="'.url('/users_img').'/'.$imageName.'"  />';

            return response(['src'=>$img,'filename'=>$imageName]);



        } elseif ($request['user_id'] == 'new_user'){


            if ($request['file_name'] != 'empty'){
                $filename = "users_img/".$request['file_name'];

                if (file_exists($filename)) {
                    unlink( public_path('users_img/'.$request['file_name'] ));
//                    unlink("users_img/".$update_user->img_url);
                }
            }


            file_put_contents("users_img/$imageName", $data);

            $img = '<img src="'.asset('users_img/').''.$imageName.'"  />';

            return response(['src'=>$img,'filename'=>$imageName]);
        }





    }

    /**
     * Удаление карты через профиль индивидуально Kairat
     */
    public function removeCardProfile(Request$request)
    {
        Card::find($request['card_id'])->delete();

    }

    /**
     * поиск городов  через профиль Kairat
     */
    public function searchCountry(Request $request)
    {
        $data = DB::table('coordinates')->where('city', 'LIKE','%'.$request->keyword.'%')->get();
        return response()->json($data); ;
    }

    /**
     * загрузка аватарки через профиль в компоненте ( vue.js ) Kairat
     */
    public function uploadImageProfile(Request $request)
    {


        $user = User::withTrashed()->find(auth()->user()->getAuthIdentifier());



        if ($user->img_url){
            $filename = "users_img/".$user->img_url;
            if (file_exists($filename)) {
                unlink(public_path('users_img/'.$user->img_url));
            }
        }




        if ($request->file == "null" || $request->file == 'undefined'){
            $user->img_url = null;
            $user->save();

            $img = '<img src="'.url('/users_img').'/'.'noavatar.png'.'" alt="avatar" />';

            return response(['img'=>$img,'filename'=>'noavatar.png','type'=>0]);

        } else {

            $request->validate([
                'file' => 'required|mimes:jpg,jpeg,png'
            ]);



            $upload_path = public_path('users_img/');
            $generated_new_name = time() . '.' .'png';
            $request->file->move($upload_path, $generated_new_name);
            $user->img_url = $generated_new_name;
            $user->save();

            $img = '<img src="'.url('/users_img').'/'.$generated_new_name.'" alt="avatar" />';
            return response(['img'=>$img,'filename'=>$generated_new_name,'type'=>1]);
        }



    }

    /**
     * Kairat uploadCroppedImageProfile
     */
    public function uploadCroppedImageProfile(Request $request)
    {


        $user = User::withTrashed()->find(auth()->user()->getAuthIdentifier());



        if ($user->cropped_img_url){
            $filename = "cropped_users_img/".$user->cropped_img_url;
            if (file_exists($filename)) {
                unlink(public_path('cropped_users_img/'.$user->cropped_img_url));
            }
        }




        if ($request->file == "null" || $request->file == 'undefined'){
            $user->cropped_img_url = null;
            $user->save();

            $img = '<img src="'.url('/cropped_users_img').'/'.'noavatar.png'.'" alt="avatar" />';

            return response(['img'=>$img,'filename'=>'noavatar.png','type'=>0]);

        }else{

            $request->validate([
                'file' => 'required|mimes:jpg,jpeg,png'
            ]);



            $upload_path = public_path('cropped_users_img/');
            $generated_new_name = time() . '.' .'png';
            $request->file->move($upload_path, $generated_new_name);
            $user->cropped_img_url = $generated_new_name;
            $user->save();

            $img = '<img src="'.url('/cropped_users_img/').'/'.$generated_new_name.'" alt="avatar" />';
            return response(['img'=>$img,'filename'=>$generated_new_name,'type'=>1]);
        }



    }

    /**
     * Kairat getProfileImage
     */
    public function getProfileImage(Request $request)
    {
        $user = User::find($request['id']);
        $filename = $user->img_url;
        return $filename;
    }

}
