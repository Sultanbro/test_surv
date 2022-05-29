<?php

namespace App\Http\Controllers\Admin;

use App\Components\TelegramBot;
use App\DayType;
use App\Fine;
use App\GroupPlan;
use App\Http\Controllers\Controller;
use App\Position;
use App\Salary;
use App\BPLink;
use App\TimetrackingHistory;
use App\UserAbsenceCause;
use App\UserFine;
use App\ProfileGroup;
use App\Setting;
use App\Timetracking;
use App\User;
use App\UserDescription;
use App\Trainee;
use App\Kpi;
use App\UserNotification;
use App\Models\Books\BookGroup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\External\Bitrix\Bitrix;
use App\Models\Bitrix\Lead;
use App\AnalyticsSettingsIndividually;
use App\Downloads;
use App\Http\Controllers\IntellectController as IC;
use App\BookCategory;
use App\Classes\Helpers\Phone;
use App\Models\Admin\Bonus;
use App\Classes\Helpers\Currency;
use App\Models\User\NotificationTemplate;
use App\Models\Analytics\Activity;
use App\Models\Analytics\KpiIndicator;
use App\Models\Admin\ObtainedBonus;
use App\Models\Admin\EditedBonus;
use App\Models\Admin\EditedKpi;
use App\Timeboard\UserPresence;
use App\PositionDescription;
use App\ProfileGroupUser as PGU;
use App\Book;

class TimetrackingController extends Controller
{
    public function __construct()
    {
        View::share('title', 'Табель сотрудников');
        View::share('menu', 'timetracking');
        $this->middleware('auth');
    }

    public function settings()
    {



        View::share('title', 'Настройки');



        View::share('menu', 'timetrackingsetting');

        $groups = ProfileGroup::where('active', 1)->get()->pluck('name','id');






        $archived_groups = ProfileGroup::where('active', 0)->get(['id', 'name']);
        $book_groups = BookGroup::all();
        
        
        /////////////////////
        $_positions = Position::all();

        $array = [];
        foreach ($_positions as $key => $position) {
            $array[$position->id] = $position->position;
        }
        $positions = $array;

        ////////////
        $active_tab = 1;
        
        if(isset($_GET['tab'])) {
            $active_tab = (int)$_GET['tab'];  
        }
        
        $roles = Auth::user()->roles ? Auth::user()->roles : [];



//        dd($roles);

        if(array_key_exists('page22', $roles) && $roles['page22'] == 'on') {} 
        elseif(array_key_exists('persons', $roles) && $roles['persons'] == 'on' && $active_tab == 1) {} 
        else {
            return redirect('/');
        }
        
        $corpbooks = [];
        if($active_tab == 3) {
            if($_SERVER['HTTP_HOST'] == env('ADMIN_DOMAIN', 'admin.u-marketing.org')) {
                $corpbooks = BookCategory::where('parent_cat_id', NULL)->where('is_deleted', 0)->get();
            } else {
                $corpbooks = collect([]);
            }
        }

        $tab5 = [
            'users' => [],
            'templates' => [],
            'positions' => [],
        ];
        
        if($active_tab == 5) {

            $users = User::withTrashed()->where('UF_ADMIN', '1')->select(DB::raw("CONCAT_WS(' ',ID, last_name, name) as name"), 'ID as id')->get()->toArray();
            $tab5['users'] = array_values($users);

            $positions = Position::select('position as name', 'id')->get()->toArray();
            $tab5['positions'] = array_values($positions);    



        }

        /// временно
        $getUsers = User::on()->limit(45)->select('id','name','last_name')->get()->toArray();




        $groupsWithId = ProfileGroup::select('name','id')->get();
        return view(
            'admin.settingtimetracking',compact('getUsers'))
            ->with('positions', $positions)
            ->with('groups',$groups)
            ->with('archived_groups',$archived_groups)
            ->with('book_groups',$book_groups)
            ->with('corpbooks',$corpbooks)
            ->with('active_tab',$active_tab)
            ->with('tab5',$tab5)
            ->with('groupsWithId',$groupsWithId);
    }

    public function fines()
    {
        View::share('menu', 'fines');
        $fines = Fine::all();
        return view('admin.fines', compact('fines'));
    }

    public function info()
    {
        View::share('menu', 'info');
        View::share('title', 'Частые вопросы');
        return view('admin.info');
    }

    public function addPosition(Request $request) {
        $pos = Position::where('position', $request->position)->first();
        if($pos) {
            return [
                'code' => 201,
            ];
        } else {
            Position::create([
                'position' => $request->position
            ]);
            return [
                'code' => 200,
                'pos' => $request->position
            ];
        }
    }

    public function deletePosition(Request $request) {
        $pos = Position::where('position', $request->position)->first();

        if($pos) {
            $pos->delete();
        }
    }

    public function getPosition(Request $request) {
        $pos = Position::where('position', $request->name)->first();

        $pd = PositionDescription::where('position_id', $pos->id)->first();

        $pos->desc = [
            'require' => $pd ? $pd->require : '',
            'actions' => $pd ? $pd->actions : '',
            'time' => $pd ? $pd->time : '',
            'salary' => $pd ? $pd->salary : '',
            'knowledge' => $pd ? $pd->knowledge : '',
            'next_step' => $pd ? $pd->next_step : '',
            'show' => $pd ? $pd->show : 0,
        ];
        
        return $pos;
    }

    public function savePositions(Request $request)
    {
        $pos = Position::find($request->id);

        $pos->position = $request->new_name;
        $pos->indexation = $request->indexation;
        $pos->sum = $request->sum;
        $pos->save();
        
        if($request->desc) {
            $pd = PositionDescription::where('position_id', $request->id)->first();
            if($pd) $pd->delete();
            $pd = new PositionDescription();
            $pd->position_id = $request->id;
            $pd->require = $request->desc['require'];
            $pd->actions = $request->desc['actions'];
            $pd->time = $request->desc['time'];
            $pd->salary = $request->desc['salary'];
            $pd->knowledge = $request->desc['knowledge'];
            $pd->next_step = $request->desc['next_step'];
            $pd->show = $request->desc['show'];
            $pd->save();

        }
        

        return [
            'pos' => $pos,
            'positions' => Position::get()->pluck('position')->toArray(),
        ];
    }

    public function positions()
    {
        $positions = Position::all();

        $array = [];
        foreach ($positions as $key => $position) {
            $array[] = $position->name;
        }
        $data['positions'] = $array;

        return response()->json($data);
    }

    

    public function addsettings(Request $request)
    {
        $position = new Position();
        $position->position = $request->position;
        $position->save();
        return 'true';
    }

    public function deletesettings(Request $request)
    {
        $deleted = Position::findOrFail($request->id);
        $deleted->delete();
        return 'true';
    }

    public function saveMinutesFromWorktimePeriod($running)
    {
        $t_start = strtotime(json_decode(json_encode($running->enter), true)['date']);
        $t_end = strtotime(json_decode(json_encode($running->exit), true)['date']);

        $running->total_hours = round(($t_end - $t_start)/60);;
        $running->updated_at = date('Y-m-d H:i:s');

        return $running->save() ? true : false;
    }

    public function timetracking(Request $request)
    {
        $user = User::bitrixUser();
        $user_timezone = ($user->timezone >= 0) ? $user->timezone : 6;
        $tz = Setting::TIMEZONES[$user_timezone];

        $groups = ProfileGroup::where('active', 1)->get();

        $message = '';
        $action = '';

        $user_groups = $user->profileGroups();

        $dt = Carbon::now($tz)->format('d.m.Y');

        $worktime_start = Carbon::parse($dt . ' 08:30', $tz);
        $worktime_end = Carbon::parse($dt . ' ' . $user_groups->max('work_end'), $tz);

        $running = $user->timetracking()->running()->first();

        if (!is_null($running)) {
            $action = 'started';

            if ($worktime_end->isPast()) {

                $running->exit = $worktime_end;
                $running->save();
                $action = 'stopped';

            } elseif ($request->stop) {

                $running->exit = Carbon::now($tz);
                $running->save();
                $action = 'stopped';

            }

        } else {
            if ($request->start) {

                if ($user->canWorkThisDay()) {
                    $message = 'Вы не можете работать в выходной день';
                } else if ($worktime_start->isFuture()) {
                    $message = 'Вы можете начать день до ' . $worktime_start->format('H:i');
                } else if ($worktime_end->isPast()) {
                    $message = 'Вы не можете работать после ' . $worktime_end->format('H:i');
                } else {
                    $tt = Timetracking::where('user_id', $user->id)->whereDate('enter', date('Y-m-d'))->first();
                    if($tt && $user->program_id == 1) {
                        $message = 'Вы уже начали день!';
                    } else {
                        
                        $running = Timetracking::create([
                            'enter' => Carbon::now($tz),
                            'user_id' => $user->id,
                        ]);
                        $action = 'started'; 
                    }
                    
                }

            }

        }
        $error = [];

        if ($message != '') {
            $error = [
                'error' => [
                    'message' => $message,
                ],
            ];
        }


        // corp book page 

        if(!$user->readCorpBook()) {
            $has_corp_book = true;
            $page = Book::getRandomPage();
        } else {
            $page = null;
            $has_corp_book = false;
        }


        return [
                'status' => $action,
                'corp_book' => [
                    'has' => $has_corp_book,
                    'page' => $page,
                ],
        ] + $error;
    }

    public function trackerstatus(Request $request)
    {
        $user = User::bitrixUser();
        
        $running = $user->timetracking()->running()->first();

        // @TODO В userController есть похожая фунцкия

        $groupsall = $user->headInGroups();

        ////////////////////////////////////////////// end of TODO
        
        $status = 'stopped';
        if (!is_null($running)) {
            $status = 'started';
        }
        
        
        $currency_rate = in_array($user->currency, array_keys(Currency::rates())) ? (float)Currency::rates()[$user->currency] : 0.0000001;
        
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
        $total_earned = number_format(round(($salary + $kpi + $bonus) * $currency_rate), 0, '.', '\'') . ' ' . strtoupper($user->currency);


         // corp book page 

         if(!$user->readCorpBook()) {
            $has_corp_book = true;
            $page = Book::getRandomPage();
        } else {
            $page = null;
            $has_corp_book = false;
        }

        return response()->json([
            'status' => $status,
            'groupsall' => $groupsall,
            'orders' => $this->getGroupOrders(),
            'zarplata' => number_format((float)$salary * $currency_rate, 0, '.', '\''). ' ' . strtoupper($user->currency),
            'bonus' => number_format(round((float)$bonus * $currency_rate), 0, '.', '\'') . ' ' . strtoupper($user->currency),
            'total_earned' => $total_earned,
            'corp_book' => [
                'has' => $has_corp_book,
                'page' => $page,
            ],
        ]);
    }
    
    public function orderPersonsToGroup(Request $request) {
        $group = ProfileGroup::find($request->group_id);

        if($group) {
            $group->required = $request->required;
            $group->save(); 
        } 
        
        return $this->getGroupOrders();

    }

    private function getGroupOrders() {
        /// Заказы руководителей
        $orders = [];
        $orderGroups = Auth::user()->headInGroups();

        if(Auth::user()->position_id == 46)  { // Старший рекрутер
            $orderGroups = ProfileGroup::where('active', 1)->get();
        }
            
        foreach ($orderGroups as $group) {
            $orders[] = [
                'group' => $group->name,
                'required' => $group->required,
                'fact' => $group->provided . ' ',
            ];
        }
        return $orders;
    }

    public function savegroup(Request $request)
    {
        $pg = ProfileGroup::where('name', $request->group)->first();

        if($pg) {
            return response()->json([
                'status' => 0,
                'group' => $pg,
            ]);
        } else {
            $added = ProfileGroup::create([
                'name' => $request->group,
            ]);
            return response()->json([
                'status' => 1,
                'group' => $added,
            ]);
        }
        
        
    }

    public function deletegroup(Request $request)
    {
        $group = ProfileGroup::where('name', 'like', '%' . $request->group . '%')->first();
        $group->active = 0;
        $group->save();
        return 'true';
    }

    public function getusersgroup(Request $request)
    {
        if ($request->group) {
            $group = ProfileGroup::where('name', 'like', '%' . $request->group . '%')->first();
            if ($group->users != null) {
                $users = json_decode($group->users);
                $users = User::whereIn('id', $users)->get(['id', DB::raw("CONCAT(name,' ',last_name,'-',email) as email")]);
            }
            $book_groups = BookGroup::whereIn('id', json_decode($group->book_groups))->get();

            if($_SERVER['HTTP_HOST'] == env('ADMIN_DOMAIN', 'admin.u-marketing.org')) {
                $corp_books = BookCategory::whereIn('id', json_decode($group->corp_books))->get();
            } else {
                $corpbooks = collect([]);
            }

            
            $bonus = Bonus::where('group_id', $group->id)->first();
        } else {
            $users = User::get(['id', DB::raw("CONCAT(name,' ',last_name,'-',email) as email")]);

            foreach($users as $user) {
                if($user->email == '') $user->email = 'x'; 
            }
        }

        $bonuses = isset($group) ? Bonus::where('group_id', $group->id)->get() : [];

        $activities = isset($group) ? Activity::where('group_id', $group->id)->get(['name', 'id'])->toArray() : [];

        $payment_terms =  isset($group) ? $group->payment_terms : '';

        //time_exceptions

        return response()->json([
            'users' => isset($users) ? $users : [],
            'book_groups' => isset($book_groups) ? $book_groups : [],
            'corp_books' => isset($corp_books) ? $corp_books : [],
            'group_id' => isset($group) ? $group->id : 0,
            'timeon' => isset($group->work_start) ? $group->work_start : "00:00",
            'timeoff' => isset($group->work_end) ? $group->work_end : "00:00",
            'plan' => isset($group->plan) ? $group->plan : 0,
            'zoom_link' => isset($group->zoom_link) ? $group->zoom_link : '',
            'bp_link' => isset($group->bp_link) ? $group->bp_link : '',
            'bonuses' => $bonuses,
            'activities' => $activities,
            'payment_terms' => $payment_terms,
            'time_exceptions' => isset($group) ? $group->time_exceptions : [],
            'time_address' => isset($group) ? $group->time_address : 0,
            'workdays' => isset($group) ? $group->workdays : 6,
            'editable_time' => isset($group) ? $group->editable_time : 0,
            'paid_internship' => isset($group) ? $group->paid_internship : 0,
            'show_payment_terms' => isset($group) ? $group->show_payment_terms : 0,
            'groups' => ProfileGroup::where('active', 1)->get()->pluck('name'),
            'archived_groups' => ProfileGroup::where('active', 0)->get(['name', 'id']),

        ]);
    }

    public function saveusersgroup(Request $request)
    {
        $group = ProfileGroup::where('name', 'like', '%' . $request->group . '%')->first();
        //
        $users_id = [];
        $groups = ProfileGroup::where('active', 1)->get();
        foreach ($request['users'] as $user) {
            $users_id[] = $user['id'];
        }
        //
        $book_groups = [];
        foreach ($request['book_groups'] as $book_group) {
            $book_groups[] = $book_group['id'];
        }

        $corp_books = [];
        foreach ($request['corp_books'] as $corp_book) {
            $corp_books[] = $corp_book['id'];
        }

        //
        $group->work_start = $request['timeon'];
        $group->work_end = $request['timeoff'];
        $group->users = json_encode(array_unique($users_id));
        $group->book_groups = json_encode(array_unique($book_groups));
        $group->corp_books = json_encode(array_unique($corp_books));
        $group->name = $request['gname'];
        $group->zoom_link = $request['zoom_link'];
        $group->bp_link = $request['bp_link'];
        $group->workdays = $request['workdays'];
        $group->payment_terms = $request['payment_terms'];
        $group->editable_time = $request['editable_time'];
        $group->paid_internship = $request['paid_internship'];
        $group->show_payment_terms = $request['show_payment_terms'];
        $group->save();


        // save users migrations

        $pgu = PGU::where('group_id', $group->id)
            ->where('date', Carbon::now()->day(1)->format('Y-m-d'))
            ->first();

        if($pgu) {

            $fired = $pgu->fired;
            $fired = array_diff($fired, array_unique($users_id));
            $fired = array_values($fired);
            $pgu->fired = $fired;

            $pgu->assigned = array_values(array_unique($users_id));
            
            $pgu->save();
        }

        //// book



        $bplink = BPLink::where('name', $request['bp_link'])->first();
        
        if($bplink) {
            $bplink->link = $request['zoom_link'];
            $bplink->save();
        } else {
            $bplink = new BPLink;
            $bplink->name = $request['bp_link'] ?? 'NONAME' . $group->id;
            $bplink->link = $request['zoom_link'] ?? 'NONAME' . $group->id;
            $bplink->save();
        }
        
        
        return [
            'groups' => ProfileGroup::pluck('name')->toArray(),
            'group' => $group->name
        ];;
    }

    public function applyPerson(Request $request) {
        
        $trainee = Trainee::where('user_id', $request->user_id)->first();
        
        $trainee->requested = now();
        $trainee->applied = now();
        $trainee->save();

        UserDescription::make([
            'user_id' => $request->user_id,
            'applied' => now(),
            'is_trainee' => 0,
        ]);

        $user = User::find($request->user_id);
        
        

        ///////////////////////////////////////////    
        $editPersonLink = 'https://admin.u-marketing.org/timetracking/edit-person?id=' . $request->user_id;
        $recruiters = User::where('position_id', 46)->get();

        $timestamp = now();

        $msg = '<a href="' . $editPersonLink . '" target="_blank">' . $user->last_name . ' ' . $user->name  . ' </a><br> ';
        $msg .= 'Рабочий график: ' . $request->schedule;

        $notification_receivers = NotificationTemplate::getReceivers(7);
                
        foreach($notification_receivers as $user_id) {
            UserNotification::create([
                'user_id' => $user_id,
                'about_id' => $request->user_id,
                'title' => 'Подготовьте документы на принятие на работу',
                'group' => $timestamp,
                'message' => $msg
            ]);
        }

        //////////////////////// Set old notification read
        $absent_notifications = UserNotification::where('about_id',$request->user_id)
            ->where(function($query) {
                $query->where('title', 'Пропал с обучения')
                    ->orWhere('title', 'Пропал с обучения: 1 день')
                    ->orWhere('title', 'Пропал с обучения: 2 день');
            })
            ->get();
        
        foreach($absent_notifications as $noti) {
            $noti->read_at = now();
            $noti->save();
        }
        ///////////////////////////////////// 
        TimetrackingHistory::create([
            'author_id' => User::bitrixUser()->id,
            'author' => User::bitrixUser()->name.' '.User::bitrixUser()->last_name,
            'user_id' => $request->user_id,
            'description' => 'Заявка на принятие на работу стажера',
            'date' => date('Y-m-d')
        ]);



        // убрать отметку стажера в этот день

        $daytypes = DayType::where('user_id', $request->user_id)->whereDate('date', date('Y-m-d'))->get();

        foreach($daytypes as $dt) {
            $dt->delete();
        }

        // group provided increment

        $group = ProfileGroup::find($request->group_id);
        if($group) {
            $group->provided = $group->provided + 1;
            $group->save();
        }
        
        // Приглашение в битрикс

        $whatsapp = new IC();
        $wphone = Phone::normalize($user->phone);
        $invite_link = 'https://infinitys.bitrix24.kz/?secret=bbqdx89w';
        //$whatsapp->send_msg($wphone, 'Ваша ссылка для регистрации в портале Битрикс24: %0a'. $invite_link . '.  %0a%0aВойти в учет времени: https://admin.u-marketing.org/login. %0aЛогин: ' . $user->email . ' %0aПароль: 12345.%0a%0a *Важно*: Если не можете через некоторое время войти в учет времени, попробуйте войти через e-mail, с которым зарегистрировались в Битрикс.');

        $lead = Lead::where('user_id', $user->id)->orderBy('id', 'desc')->first();
            if($lead && $lead->deal_id != 0) {
                $bitrix = new Bitrix();

                $bitrix->changeDeal($lead->deal_id, [
                    'STAGE_ID' => 'C4:WON' // не присутствовал на обучении
                ]);
            }

        return [
            'msg' => 'Заявка отправлена рекрутерам'
        ];
    }

    public function reports(Request $request)
    {   
        $roles = \Auth::user()->roles ? \Auth::user()->roles : [];
        
        if(array_key_exists('page21', $roles) && $roles['page21'] == 'on') {}
        else {
            return redirect('/');
        }

        View::share('menu', 'timetrackingreports');
        $groups = ProfileGroup::where('active', 1)->get();
        $fines = Fine::selectRaw('id as value, CONCAT(name," <span>(-", penalty_amount,")</span>") as text')->get();
        $years = ['2020', '2021', '2022']; // TODO Временно. Нужно выяснить из какой таблицы брать динамические годы
        return view('admin.reports', compact('groups', 'fines', 'years'));
    }

    public function getReports(Request $request)
    {

        $year = $request['year'];

        if ($request['group_id']) {
            $group = ProfileGroup::find($request['group_id']);
            if (!empty($group) && $group->users != null) {
                $users_ids = json_decode($group->users);
                $head_ids = json_decode($group->head_id);
            }
        }

        $currentUser = User::bitrixUser();
        $group_editors = is_array(json_decode($group->editors_id)) ? json_decode($group->editors_id) : [];
        // Доступ к группе
        if (!in_array($currentUser->id, $group_editors) && $currentUser->id != 18) {
            return [
                'error' => 'access',
            ];
        }
        
        /**
         * Выбираем кого покзаывать
         */

        if($request->user_types == 0) { // Действующие
            $_user_ids = User::leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('ud.is_trainee', 0)
                ->where('UF_ADMIN', 1)
                ->whereIn('users.id', $users_ids)
                ->get()
                ->pluck('id')
                ->toArray();
            
            // foreach($users_ids as $user_id) {
            //     $trainee = Trainee::whereNull('applied')->where('user_id', $user_id)->first(); 
                
            //     if($trainee) {
            //         if (($key = array_search($user_id, $_user_ids)) !== false) {
            //             unset($_user_ids[$key]);
            //         }
            //     }
            // }
 
            
        }
        
        if($request->user_types == 1) { // Уволенныне
            $_user_ids = User::onlyTrashed()->whereIn('id', $users_ids)->pluck('id')->toArray();
            //////////////////////
            $date = $year . '-' . $request->month . '-01';
            $date_for_register = Carbon::parse($date); 
            $date_for_fire = Carbon::parse($date)->startOfMonth();
            $d_users = User::onlyTrashed()->where('UF_ADMIN', 1)
                //->whereDate('created_at', '<', $date_for_register)
                ->whereDate('deleted_at', '>=', $date_for_fire)
                ->get();
            
            foreach($d_users as $d_user) {
                if($d_user->last_group != NULL) {
                    $lg = json_decode($d_user->last_group);
                    if(in_array($request['group_id'], $lg)) {
                        array_push($_user_ids, $d_user->id);
                    }
                } 
            } 
            
            $_user_ids = User::onlyTrashed()->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('ud.is_trainee', 0)
                ->where('UF_ADMIN', 1)
                ->whereIn('users.id', $_user_ids)
                ->get()
                ->pluck('id')
                ->toArray();
        }

        if($request->user_types == 2) { // Стажеры

            $_user_ids = User::leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('ud.is_trainee', 1)
                ->where('UF_ADMIN', 1)
                ->whereIn('users.id', $users_ids)
                ->get()
                ->pluck('id')
                ->toArray();
            
        }


        

        //////////////////////

        $users = Timetracking::getTimeTrackingReportPaginate($request, $_user_ids, $year);
        

        $data = [];

        $data['pagination'] = [
            'total' => $users->total(),
            'perPage' => $users->perPage(),
            'lastPage' => $users->lastPage(),
            'currentPage' => $users->currentPage(),
            'nextPageUrl' => $users->nextPageUrl(),
            'previousPageUrl' => $users->previousPageUrl()
        ];

        $data['head_ids'] = $head_ids;
        $data['total_resources'] = 0;
        $month = Carbon::createFromFormat('m-Y', $request->month . '-' . $year);
        

        $data['users'] = [];

    
        foreach ($users as $user) {
            $fines = [];
            $daytypes = [];
            $weekdays = [];
   
            $days = $user->daytypes->whereIn('type', [5,7])->sortBy('day')->toArray();
          
            $data['total_resources'] += $user->full_time == 1 ? 1 : 0.5;
           // if(count($days) > 1) $enable_comment_for_trainee_2 = $days[1]['day'];
                
            $enable_comment = [ // для стажера
                '1' => 0,
                '2' => 0,
            ];

            for ($i = 1; $i <= $month->daysInMonth; $i++) {
                $fines[$i] = $user->fines->where('date', $i)->where('status', 1)->pluck('fine_id') ?? [];

                $x = $user->daytypes->where('day', $i)->first();
                $daytypes[$i] = $x->type ?? null;

                $weekdays[$i] = $user->weekdays[(int)$month->day($i)->dayOfWeek] == 1 ? 1 : 0;
               
                if($x && in_array($x->type,[2,5,7]) && $enable_comment['1'] == 0) {
                    $enable_comment['1'] = $x->day;
                }
                if($x && in_array($x->type,[2,5,7]) && $enable_comment['1'] != $i && $enable_comment['2'] == 0) {
                    $enable_comment['2'] = $x->day;
                }
            }
            
            $user->enable_comment = $enable_comment;

            $user->selectedFines = $fines;
            $user->dayTypes = $daytypes;
            $user->weekdays = $weekdays;

            $user_applied_at = UserDescription::where('user_id', $user->id)->first();
            if($user_applied_at && $user_applied_at->applied) {
                $user_applied_at = $user_applied_at->applied;
            } else {
                $user_applied_at = $user->created_at;
            }

            if($user_applied_at && Carbon::parse($user_applied_at)->month == $request->month && Carbon::parse($user_applied_at)->year == $request->year) {
                $user->applied_at = Carbon::parse($user_applied_at)->day;
            } else {
                $user->applied_at = 0;
            }

            
            $data['users'][] = $user;
            // if(self::showFiredEmployee($user, $request->month, $year) == true) { // Проверка не уволен ли сотрудник
            //     $data['users'][] = $user;
            // }
             
            
            
            foreach($user->timetracking as $tt) {
                $tt->minutes = $tt->total_hours;
            }


            //$trainee = Trainee::whereNull('applied')->where('user_id', $user->id)->first();
            $trainee = UserDescription::where('is_trainee', 1)->where('user_id', $user->id)->first();

            if($trainee) {
                $user->is_trainee = true;
                $user->requested = $trainee->applied ? date('d.m.Y H:i', Carbon::parse($trainee->applied)->timestamp + 3600 * 6) : null; // $requested
            } else {
                $user->is_trainee = false;
                $user->requested = null; // $requested
            }
            // if(Auth::user()->id == 5 && $user->id == 6093) {
            //     dd($user->daytypes);
            // }

        }

        //$data['sum'] = Timetracking::getSumHoursPerMonthByUsersIds($users_ids, $request->month, $year);
        $data['sum'] = [];


        $data['editable_time'] = $group->editable_time;
        
        
        return $data;
    }

    // Проверка не уволен ли сотрудник
    private function showFiredEmployee($user, $month, $year) {
        if($user->deleted_at == '0000-00-00 00:00:00' || $user->deleted_at == null) { // Проверка не уволен ли сотрудник
            return true;
        } else {
            
            $dt1 = Carbon::parse($user->deleted_at); // День увольнения
            $dt2 = Carbon::create($year, $month, 30, 0, 0, 0); // Выбранный период

            if($dt1 >= $dt2) {
                if(count($user->fines) != 0) { // Проверка есть ли хоть одна fine user-a
                    return true;
                }
            } else if ($dt1->month == $dt2->month && $dt1->year == $dt2->year) { // Проверка совпадают ли месяцы
                return true;
            }
        }
    }

    public function getHistory(Request $request)
    {
        if ($request['group_id']) {
            $group = ProfileGroup::find($request['group_id']);
            if (!empty($group) && $group->users != null) {
                $users_ids = json_decode($group->users);
            }
        }
        $currentUser = User::bitrixUser();

        TimetrackingHistory::where('user_id', $request->user_id)
            ->where('date', '')
            ->get();

    }

    public function updateTimetrackingDay(Request $request)
    {
        $rules = [
            'year' => 'required',
            'month' => 'required',
            'day' => 'required',
            'user_id' => 'required',
            'minutes' => 'required'
        ];

        $valid = validator($request->all(), $rules);

        if ($valid->fails()) {
            return response()->json($valid->errors()->first(), 400);
        }

        $days = Timetracking::where('user_id', intval($request->user_id))
            ->whereYear('enter', intval($request->year))
            ->whereMonth('enter', intval($request->month))
            ->whereDay('enter', $request->day)
            ->selectRaw('*, TIMESTAMPDIFF(minute, `enter`, `exit`) as minutes')
            ->orderBy('id', 'ASC')
            ->get();
        $day = $days->first();


        // Проверка не начинал ли сотрудник работу ранее рабочего времени
        $timeStart = self::checkStartOfDay($request, $day);

        $minutes = $request->minutes;
        if($request->minutes > 300) { // Если человек работал больше 5 часов
            $minutes += 60;  // то добавляется 1 час к обеду
        }
        // Добавить новый exit
        $exit = Carbon::parse($timeStart)->addMinutes(intval($minutes));
        //Конец блока 

        if (count($days) > 1) {
            $items = $days->except($days->first()->id)->pluck('id');
            Timetracking::whereIn('id', $items)->delete();
        }
        
        $employee = User::withTrashed()->find($request->user_id);
        if (count($days) > 0) {
            if($day->exit == null) {
                $day->exit = $exit;
            }
           
            $day->total_hours = intval($request->minutes);
            $day->updated = 1;
            $day->save();

            $author = Auth::user();
            $description = 'часов работы c ' . number_format(floatval($request->before / 60), 2, '.', '') . ' на ' . number_format(floatval($request->minutes / 60), 2, '.', '');
            $authorName = '' . ($author->name) . ' ' . ($author->last_name) . '';

            $history = TimetrackingHistory::create([
                'author_id' => Auth::user()->id,
                'author' => $authorName,
                'user_id' => $request['user_id'],
                'date' => Carbon::parse($timeStart)->format('Y-m-d'),
                'description' => isset($request->comment) ? $description . '. Причина:' . $request->comment : $description
            ]);
        }
        
        $result = [
            'success' => true,
            'history' => $history ?? null
        ];
        return response()->json($result, 200);
    }

    // Проверка не начинал ли сотрудник работу ранее рабочего времени
    public static function checkStartOfDay($request, $day) {

        $userProfile = DB::table('users')
                        ->select('*')
                        ->where('id', '=', $day->user_id)
                        ->first();
        
        $workStart = $userProfile->work_start;

        $workStartInSeconds = strtotime($request->year.'-'.$request->month.'-'.$request->day.' '.$workStart);

        $timeStart = $day->enter;

        if ($workStartInSeconds > strtotime($day->enter)) {
            $timeStart = $request->year.'-'.$request->month.'-'.$request->day.' '.$workStart;
        }

        return $timeStart;
    }

    public function getNotificationTemplates(Request $request) {
        
        $_users = NotificationTemplate::where('type', NotificationTemplate::USER)->get();        

        foreach($_users as $record) {
            $users = json_decode($record->ids,true);
            $selected = User::select(DB::raw("CONCAT_WS(' ',ID, last_name, name) as name"),'id')->whereIn("id", $users)->get();
            $record->selectedGroups = $selected->toArray();
        }

        $_groups = NotificationTemplate::where('type', NotificationTemplate::GROUP)->get();        

        foreach($_groups as $record) {
            $groups = json_decode($record->ids,true);
            $selectedGroups = ProfileGroup::select('name','id')->whereIn("id", $groups)->get();
            $record->selectedGroups = $selectedGroups->toArray();
        }
        
        $_positions = NotificationTemplate::where('type', NotificationTemplate::POSITION)->get();        

        foreach($_positions as $record) {
            $positions = json_decode($record->ids,true);
            $selectedGroups = Position::select('position as name','id')->whereIn("id", $positions)->get();
            $record->selectedGroups = $selectedGroups->toArray();
        }

        $_others = NotificationTemplate::where('type', NotificationTemplate::OTHER)->get();        

        $_notifications = User::withTrashed()
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where('ud.notifications', '!=', '[]')
            ->select(DB::raw("CONCAT_WS(' ',users.ID, users.last_name, users.name) as name"), 'users.ID as id')
            ->get()->toArray();
        
        //$_notification_templates = NotificationTemplate::where('type', NotificationTemplate::USER)->select('id', 'title as name')->get()->toArray();
        $_notification_templates = NotificationTemplate::where('type', NotificationTemplate::USER)->select('title','id')->get()->toArray();

        $need_group = NotificationTemplate::where('type', NotificationTemplate::USER)->pluck('need_group','id')->toArray();



        return [
            NotificationTemplate::USER => $_users,
            NotificationTemplate::GROUP => $_groups,
            NotificationTemplate::POSITION => $_positions,
            NotificationTemplate::OTHER => $_others,
            4 => $_notifications,
            5 => $_notification_templates,
            6 => $need_group,
        ];
    }

    public function updateNotificationTemplate(Request $request) {
       
        $ids = [];
        foreach($request->ids as $item) {
            array_push($ids,$item['id']);
        }

        $template = NotificationTemplate::find($request->id);

        if($template->type == 0) { // individual
            $old_ids = json_decode($template->ids);
            $old_ids = array_diff($old_ids, $ids);
            $old_ids = array_values($old_ids);
            
            // Удалить старых получателей
            foreach($old_ids as $id) {
                $ud = UserDescription::where('user_id', $id)->first();

                $notis = [];

                foreach($ud->notifications as $noti) {
                    if($noti[0] != $request->id) {
                        array_push($notis, $noti);
                    } 
                }
                
                $ud->notifications = $notis;
                $ud->save();
            }

            /***************** ДОбавить сотрудников к уведомлению */
            $new_ids = $ids;
            $new_ids = array_diff($new_ids, $old_ids);
            $new_ids = array_values($new_ids);

            foreach($new_ids as $id) {
                $user_was = false;

                $ud = UserDescription::where('user_id', $id)->first();

                $ns = $ud->notifications;
                foreach($ns as $noti) {
                    if($noti[0] == $request->id) {
                        $user_was = true;
                    }
                }

                if(!$user_was) {
                    array_push($ns, [$request->id, ProfileGroup::pluck('id')->toArray()]);
                    $ud->notifications = $ns;
                    $ud->save();
                }
            }

        }
   
        $template->update([
                'message' => $request->message,
                'action' => $request->action,
                'ids' => json_encode($ids),
            ]);        

        return 'Успешно изменен!'; 
    }

    public function getWorkingtDays($year, $month, $weekend = false)
    {
        $result = 0;
        $loop = strtotime("$year-$month-01");
        do if (!$weekend or !in_array(strftime("%u", $loop), $weekend))
            $result++;
        while (strftime("%m", $loop = strtotime("+1 day", $loop)) == $month);
        return $result;
    }

    public function getgroups(Request $request)
    {
        $user = User::bitrixUser();
        $groups = ProfileGroup::where('active', 1)->get();

        $array = [];
        if ($user->id !== 18) {
            foreach ($groups as $key => $group) {
                $editors_id = json_decode($group->editors_id);
                if ($editors_id !== null && in_array($user->id, $editors_id)) {
                    $array[] = $group;
                }
            }
            return $array;
        }
        return $groups;
    }

    public function usereditreports(Request $request)
    {

        if($request->page == 'page-top') {
            
            $users = User::where('roles', 'like', '%"page-top":"on"%')->get();

            foreach ($users as $user) {
                $arr = $user->roles;
                $arr['page-top'] = null;
                $user->roles = $arr;
                $user->save();
            }

            foreach ($request->users as $_user) {
                $user = User::find($_user['id']);
                if($user) {
                    if($user->roles) {
                        $arr = $user->roles;
                        $arr['page-top'] = 'on';
                        $user->roles = $arr;
                    } else {
                        $user->roles = ['page-top' => 'on'];
                    }
                    $user->save();
                }
            }
        } else {
            $group = ProfileGroup::find($request['group_id']);

            $editors_id = [];

            foreach ($request->users as $user) {
                $editors_id[] = $user['id'];
                
                $user = User::find($user['id']);
                if($user) {
                    if($user->roles) {
                        $arr = $user->roles;
                        $arr['page21'] = 'on';
                        $user->roles = $arr;
                    } else {
                        $user->roles = ['page21' => 'on'];
                    }
                    $user->save();
                } 
            }

            $old_editors = json_decode($group->editors_id);
            $group->editors_id = json_encode(array_unique($editors_id));
            $group->save();

            $all_editors = [];
            $groups = ProfileGroup::where('active', 1)->get();
            foreach($groups as $g) {
                foreach(json_decode($g->editors_id) as $user_id) {
                    array_push($all_editors, $user_id);
                }  
            } 
            $all_editors = array_unique($all_editors);

            foreach($old_editors as $older) {
                if(!in_array($older, array_unique($all_editors))) {
                    $user = User::find($older);
                    if($user && $user->roles) {
                        $arr = $user->roles;
                        $arr['page21'] = null;
                        $user->roles = $arr;
                        $user->save();
                    } 
                }
            }

        
        }
        

        return $request->users;
    }

    public function modalcheckuserrole(Request $request)
    {
        if($request->page == 'page-top') {
            $users = [];
            $users = User::where('roles', 'like', '%"page-top":"on"%')->get(['id', DB::raw("CONCAT(name,' ',last_name,'-',email) as email")]);
        } else {
            $group = ProfileGroup::find($request['group_id']);

            $editors_id = json_decode($group->editors_id);
            if (is_array($editors_id) && count($editors_id)) {

                $users = User::whereIn('id', $editors_id)->get(['id', DB::raw("CONCAT(name,' ',last_name,'-',email) as email")]);
            } else {
                $users = [];
            }
        }

        return $users;
    }

    public function enterreportManually(Request $request) {

        if($request->time == '') {
            return 'The time is not set';
        }

        $timetracking = Timetracking::where('user_id', intval($request->user_id))
            ->whereYear('enter', intval($request->year))
            ->whereMonth('enter', intval($request->month))
            ->whereDay('enter', $request->day)
            ->first();
        
        $date = $request->day.'.'.$request->month.'.'.$request->year;
        $enter = Carbon::parse($date);
        $enter->setTimeFromTimeString($request->time);
        
        if($timetracking) {
            $description = 'Изменено: '.$request->time.'. '.$request->comment;
            $timetracking->enter = $enter;
            $timetracking->user_id = $request->user_id;
            $timetracking->updated = 1;
            $timetracking->save();
        } else {
            $description = 'Добавлено: '.$request->time.'. '.$request->comment;
            Timetracking::create([
                'enter' => $enter,
                'user_id' => $request->user_id,
                'updated' => 1
            ]);
        }

        TimetrackingHistory::create([
            'author_id' => User::bitrixUser()->id,
            'author' => User::bitrixUser()->name.' '.User::bitrixUser()->last_name,
            'user_id' => $request->user_id,
            'description' => $description,
            'date' => $enter
        ]);


    }

    public function enterreport(Request $request)
    {

        $roles = \Auth::user()->roles ? \Auth::user()->roles : [];
        
        if(array_key_exists('page21', $roles) && $roles['page21'] == 'on') {}
        else {
            return redirect('/');
        }
        
        View::share('title', 'Время прихода');
        View::share('menu', 'timetrackingenters');

        $groups = ProfileGroup::where('active', 1)->get();

        $currentUser = User::bitrixUser();

        if ($request->isMethod('post')) {
            $group = ProfileGroup::find($request->group_id);
            $user_ids = json_decode($group->users);
            $user_ids = array_unique($user_ids);

            $group_editors = is_array(json_decode($group->editors_id)) ? json_decode($group->editors_id) : [];
            // Доступ к группе
            if (!in_array($currentUser->id, $group_editors) && $currentUser->id != 18) {
                return [
                    'error' => 'access',
                ];
            }

            $users = User::withTrashed()->selectRaw("*,CONCAT(name,' ',last_name) as full_name")->with(['timetracking' => function ($q) use ($request) {
                $q->selectRaw("*, DATE_FORMAT(`enter`, '%e') as date")
                    ->orderBy('date')
                    ->whereMonth('enter', $request->month)
                    ->whereYear('enter', $request->year);
            }])->whereIn('id', $user_ids)->get();

            if (is_null($users)) {
                return;
            }

            $month = Carbon::createFromFormat('m-Y', $request->month . '-' . $request->year);
            

            foreach ($users as $userData) {

                $userfines = UserFine::where('user_id', $userData->id)
                    ->whereMonth('day', $request->month)
                    ->whereYear('day', $request->year)
                    ->where('status', 1)
                    ->whereIn('fine_id', [1,2])
                    ->get();

                foreach($userfines as $fine) {
                    $fine->day = substr($fine->day ,8 , 2);
                }
                //if($userfines->count() > 0) dd($userfines);
                   
                $days = array_unique($userData->timetracking->pluck('date')->toArray());

                if(self::showFiredEmployee($userData, $request->month, $request->year) == true){
                    foreach ($days as $day) {
                        $data[$userData->id][$day] = $userData->timetracking->where('date', $day)->min('enter')->format('H:i');
                    }

                    $fines = [];
                    for ($i = 1; $i <= $month->daysInMonth; $i++) {
                        $d = $i;
                        if(strlen ($i) == 1) $d = '0' . $i;

                        $x = $userfines->where('day', $d);
                        if($x->count() > 0) {
                            $fines[$i] = ['yes'];
                        } else {
                            $fines[$i] = [];
                        }
                    }
                   

                


                    $data[$userData->id]['fines'] = $fines; 
                    $data[$userData->id]['name'] = $userData->full_name;
                    $data[$userData->id]['user_id'] = $userData->id;
                }

            }

            return array_values($data);

        }

        $years = ['2020', '2021', '2022']; // TODO Временно. Нужно выяснить из какой таблицы брать динамические годы
        return view('admin.enter-report', compact('groups', 'years'));
    }

    public function zarplatatable(Request $request)
    {
        $user = User::bitrixUser();
        // me($user->workingDay);
        // me($user->zarplata);

        /**
         * Prepare zarplata vars and count hourly pay
         */
        
        try {
            $currency_rate = (float)Currency::rates()[$user->currency];
        } catch(\Exception $e) {
            $currency_rate = 0.00001;
        }
        

        $date = Carbon::createFromDate(date('Y'), $request->month, 1);

        $hourly_pay = $user->hourly_pay($date->format('Y-m-d'));

        //$salaries = $user->getSalaryByMonth($date);
        $data = [
            'salaries' => [],
            'times' => [],
            'hours' => [],
        ];

        $request->year = date('Y');
        $userFines = UserFine::where('status', UserFine::STATUS_ACTIVE)
            ->whereYear('day', date('Y'))
            ->whereMonth('day', $request->month)
            ->where('user_id', $user->id)
            ->get();

        
        $totalFines = 0;
        foreach($userFines as $fine) {
            $_fine = Fine::find($fine->fine_id);
            if($_fine) {
                $amount = (int)$_fine->penalty_amount * $currency_rate;
                $totalFines += $amount; 
                $amount = number_format($amount,  2, '.', ',');
                $fine->name = $_fine->name.'. Сумма: '.  $amount .' '. strtoupper($user->currency);
            } else {
                $fine->name = 'Добавлен штраф без ID. Сообщите в тех.поддержку';
            }
        }
        
        
        $userFines = $userFines->groupBy(function($fine) {
            return Carbon::parse($fine->day)->format('d'); 
        });


        //bonuses
        $bonuses = Salary::where('user_id', $user->id)
            ->whereYear('date',  date('Y'))
            ->whereMonth('date', $request->month)
            ->where('bonus', '!=', 0)
            ->orderBy('id','desc')
            ->get();
        
        $total_bonuses = $bonuses->sum('bonus');

        $bonuses = $bonuses->groupBy(function($b) {
            return Carbon::parse($b->date)->format('d'); 
        });

        
        $total_bonuses += ObtainedBonus::onMonth($user->id, date('Y-m-d'));

        $obtained_bonuses = ObtainedBonus::where('user_id', $user->id)
                    ->whereYear('date', date('Y'))
                    ->whereMonth('date', $request->month)
                    ->where('amount', '>', 0)
                    ->get()
                    ->groupBy(function($b) {
                        return Carbon::parse($b->date)->format('d'); 
                    });

        // Бонусы

        $editedBonus = EditedBonus::where('user_id', $user->id)
            ->whereYear('date',  date('Y'))
            ->whereMonth('date',  $request->month)
            ->first();
            
        $total_bonuses = $editedBonus ? $editedBonus->amount : $total_bonuses;

        /**
         * KPI
         */
        $editedKpi = EditedKpi::where('user_id', $user->id)
            ->whereYear('date', date('Y'))
            ->whereMonth('date', $request->month)
            ->first();

        //avanses
        $avanses = Salary::where('user_id', $user->id)
            ->whereYear('date',  date('Y'))
            ->whereMonth('date', $request->month)
            ->where('paid', '!=', 0)
            ->orderBy('id','desc')
            ->get();
        
        $total_avanses = $avanses->sum('paid');

        $avanses = $avanses->groupBy(function($b) {
            return Carbon::parse($b->date)->format('d'); 
        });

        for($i=1;$i<=$date->daysInMonth;$i++) {
            $m = $i;
            if(strlen($i) == 1) $m = '0'.$i;
            $data['salaries'][$i]['fines'] = isset($userFines[$m]) ? $userFines[$m]: []; 
            $data['salaries'][$i]['bonuses'] = isset($bonuses[$m]) ? $bonuses[$m]: [];
            $data['salaries'][$i]['awards'] = isset($obtained_bonuses[$m]) ? $obtained_bonuses[$m]: [];
            $data['salaries'][$i]['avanses'] = isset($avanses[$m]) ? $avanses[$m]: [];

        }

        // Вычисление даты принятия
        $user_applied_at = $user->applied_at();
        
        $timetrackers = Timetracking::select([
                DB::raw('DAY(enter) as date'),
                DB::raw('sum(total_hours) as total_hours'),
                DB::raw('MIN(enter) as enter')
            ])
            ->groupBy('date')
            ->whereMonth('enter', $request->month)
            ->whereYear('enter', date('Y'))
            ->where('user_id', $user->id)
            ->whereDate('enter', '>=', Carbon::parse($user_applied_at)->format('Y-m-d'))
            ->get();
        
        $trainee_days = DayType::selectRaw("DAY(date) as datex")
            ->where('user_id', $user->id)
            ->whereYear('date',  $date->year)
            ->whereMonth('date',  $date->month)
            ->whereIn('type', [5,6,7])
            ->get(['datex']);
        
        /////  Рaбочие дни до принятия на работу
        $tts_before_apply = Timetracking::whereYear('enter', date('Y'))
            ->select([
                DB::raw('DAY(enter) as date'),
                DB::raw('sum(total_hours) as total_hours'),
                DB::raw('MIN(enter) as enter')
            ])
            ->whereMonth('enter', $request->month)
            ->whereDate('enter', '<', Carbon::parse($user_applied_at)->format('Y-m-d'))
            ->where('user_id', $user->id)
            ->groupBy('date')
            ->get();


        $days = array_unique($timetrackers->pluck('date')->toArray());

        for($day=1;$day<=$date->daysInMonth;$day++) {
            


            //count hourly pay 

            $s = Salary::where('user_Id', $user->id)
                ->where('date', $date->day($day)->format('Y-m-d'))
                ->first();
            
            $zarplata = $s ? $s->amount : 70000;
            $working_hours = $user->workingTime ? $user->workingTime->time : 9;
            $ignore = $user->working_day_id == 1 ? [6,0] : [0];   // Какие дни не учитывать в месяце
            $workdays = workdays($date->year, $date->month, $ignore);
        
            $hourly_pay = $zarplata / $workdays / $working_hours;

            $hourly_pay = round($hourly_pay, 2);


            // count 

            $data['times'][$day]['value'] = $timetrackers->where('date', $day)->count() > 0 ? $timetrackers->where('date', $day)->first()->enter->format('H:i') : '';
            $data['times'][$day]['fines'] = [];
            $data['times'][$day]['training'] = false;
            
            
            $hour = $timetrackers->where('date', $day)->count() > 0 ? $timetrackers->where('date', $day)->first()->total_hours / 60 : '';

            if ($hour < 0) {
                $hour = 0;
            }

            if($hour == '') {
                $hour = 0;
            }

            $data['salaries'][$day]['value'] = number_format(round($hour * $hourly_pay * $currency_rate), 0, '.', '');
            
            $data['salaries'][$day]['training'] = false;
           
            if($trainee_days->where('datex', $day)->first()) {
                $hour = $user->working_time_id == 1 ? 8 : 9;
                $data['salaries'][$day]['value'] = number_format(round($hour * $hourly_pay * $currency_rate * $user->internshipPayRate()), 0, '.', ''); // стажировочные на пол суммы
               // $data['salaries'][$day]['value'] = 0;
                $data['salaries'][$day]['training'] = true;
            } else if($tts_before_apply->where('date', $day)->first()) {
                $hour = $tts_before_apply->where('date', $day)->first()->total_hours / 60;
                $data['salaries'][$day]['value'] = number_format(round($hour * $hourly_pay * $currency_rate), 0, '.', '');
            }

            //if($tts_before_apply->where('date', $day)->first()) dd($tts_before_apply->where('date', $day)->first());

            $data['hours'][$day]['value'] = round($hour, 2);

            if($data['salaries'][$day]['training'] || $data['hours'][$day]['value'] == 0) $data['hours'][$day]['value'] = '';
            if($data['salaries'][$day]['value'] == 0) $data['salaries'][$day]['value'] = '';

            $data['salaries'][$day]['calculated'] =  round($hour, 2) . ' * ' . ($trainee_days->where('datex', $day)->first() ? $hourly_pay * $user->internshipPayRate() : $hourly_pay);

            $data['hours'][$day]['fines'] = [];
            $data['hours'][$day]['training'] = false;
        }
        
        // /// // / // 
        return [
            'data' => [
                'salaries' => $data['salaries'],
                'times' => $data['times'],
                'hours' => $data['hours'],
            ],
            'totalFines' => $totalFines,
            'total_avanses' => $total_avanses
        ];
    }

    public function setDay(Request $request)
    {
        $request->validate([
            //'type' => 'in:' . implode(',', array_values(DayType::DAY_TYPES)),
            'user_id' => 'exists:users,ID',
        ]);

        $user = User::bitrixUser();
        $targetUser = User::find($request->user_id);
           
        if($targetUser == null) {return ['success' => 1, 'history' => null];}

        $year = date('Y');
        $date = Carbon::parse($year . '-' . $request->month . '-' . $request->day);
        $daytype = DayType::where('user_id', $request->user_id)->where('date', $date->format('Y-m-d'))->first();
        //info($daytype);
        // if ($request->type == 0 && isset($daytype)) {
        //     return ['success' => 0, 'history' => null];
        // }
        if (!$daytype) {
            $daytype = DayType::create([
                'user_id' => $request->user_id,
                'type' => $request->type,
                'email' => $targetUser->email,
                'date' => $date,
                'admin_id' => $user->id,
            ]);
            $description = 'с обычного на ' . DayType::DAY_TYPES_RU[$request->type];
        } else {

            

            $description = 'с ' . DayType::DAY_TYPES_RU[$daytype->type] . ' на ' . DayType::DAY_TYPES_RU[$request->type];
            $daytype->type = $request->type;
            $daytype->admin_id = $user->id;
            $daytype->save();
        }
        
        $authorName = $user->name . ' ' . $user->last_name;
        $desc = isset($request['comment']) ? $description . '. Причина: ' . $request['comment'] : $description;

        $history = TimetrackingHistory::create([
            'user_id' => $request->user_id,
            'author_id' => $user->id,
            'author' => $authorName,
            'date' => $date,
            'description' => $desc,
        ]);


        if ($request->type == 1) { // Выходной  
            $fines = UserFine::where('day', $date)
            ->where('user_id', '=',  $targetUser->id)
            ->get();

            foreach($fines as $fine) {
                $fine->status = UserFine::STATUS_INACTIVE;
                $fine->save();
            }

            $salary = Salary::where('date', $date)->where('user_id',  $targetUser->id)->first();
            if($salary)  {
                $salary->amount = 0;
                $salary->save(); 
            }

        }
        /**     
         * TODO Тут нет условия если не стажер
         */
        if ($request->type == 2) { // Отсутсвует 

            $trainee = UserDescription::where('is_trainee', 1)->where('user_id', $request->user_id)->first();
            
            if($trainee) {
                $editPersonLink = 'https://admin.u-marketing.org/timetracking/edit-person?id=' . $request->user_id;
                $recruiters = User::where('position_id', 46)->get();
                
                // Поиск ID лида или сделки
                if($trainee->lead_id != 0) {
                    $lead_id = $trainee->lead_id;
                } else { 
                    $lead = Lead::where('phone', Phone::normalize($targetUser->phone))->orderBy('id', 'desc')->first();
                    if($lead) {
                        $lead_id = $lead->lead_id;
                    } else {
                        $lead_id = 0;
                    }
                }

                $lead = Lead::where('user_id', $request->user_id)->first();
                if($lead) {
                    $lead->status = 'ABSENT';
                    $lead->save();
                }
                // Пропал с обучения

                $typex = UserAbsenceCause::THIRD_DAY;
                $title_lost = 'Пропал с обучения';
                $notification_temp_id = 2;
                if(array_key_exists('1', $request->enable_comment) && array_key_exists('2', $request->enable_comment)) {
                    if($request->day == $request->enable_comment['1']) {
                        $title_lost = 'Пропал с обучения: 1 день';
                        $notification_temp_id = 4;
                        $typex = UserAbsenceCause::FIRST_DAY;
                    } else if($request->day == $request->enable_comment['2']) {
                        $title_lost = 'Пропал с обучения: 2 день';
                        $notification_temp_id = 5;
                        $typex = UserAbsenceCause::SECOND_DAY;
                    }
                }


                // Причина отсутствия 1 2 3 дни
                UserAbsenceCause::createOrUpdate([
                    'user_id' => $request->user_id,
                    'date' => $date->day(1)->format('Y-m-d'),
                    'type' => $typex,
                    'text' => $request->comment,
                ]);


                //    
                $nootis = UserNotification::where([
                    'title' => $title_lost,
                    'about_id' => $targetUser->id,
                ])->get();

                foreach($nootis as $noti) {
                    $noti->read_at = now();
                    $noti->save();
                }

                ////////// notify
                $g = ProfileGroup::find($request->group_id);
                $group_name = $g ? '(' . $g->name . ')' : '';

                $abs_msg = $authorName . ': '. $group_name .'  Стажер не был на обучении: <br> <a href="' . $editPersonLink . '" target="_blank">';
                $abs_msg .= $targetUser->last_name . ' ' . $targetUser->name  . ' </a>';
                $abs_msg .= '<br><a href="/timetracking/analytics/skypes/' . $lead_id . '" target="_blank" class="btn btn-primary mr-2 mt-2 rounded btn-sm">Перейти в сделку</a>';
                $abs_msg .= '<a class="btn btn-primary mt-2 rounded btn-sm transfer-training" data-userid="' . $targetUser->id . '">Перенести обучение</a>';

                $timestamp = now(); 

                $notification_receivers = NotificationTemplate::getReceivers($notification_temp_id);
                
                foreach($notification_receivers as $user_id) {
                    UserNotification::create([
                        'user_id' => $user_id,
                        'about_id' => $targetUser->id,
                        'title' => $title_lost,
                        'message' => $abs_msg,
                        'group' => $timestamp
                    ]);
                    
                }

                ///////// // перенос сделки с Обучается на Пропал с обучения в БИТРИКС

                $bitrix = new Bitrix();

                if($trainee->deal_id != 0) {
                    $deal_id = $trainee->deal_id;
                } else if($lead_id != 0) {
                    $deal_id = $bitrix->findDeal($lead_id, false);
                    usleep(1000000); // 1 sec
                } else {
                    $deal_id = 0;
                }  
                
                if($deal_id != 0) {
                    $bitrix->changeDeal($deal_id, [
                        'STAGE_ID' => 'C4:21' 
                    ]);
                }
                /////-*-*-*-----------*-*-*-*-*-*-*//
                


            }

            
            if(in_array((int)$date->dayOfWeek, [0,1])) {
                $kaspi35 = json_decode(ProfileGroup::find(35)->users);
                $kaspi42 = json_decode(ProfileGroup::find(42)->users);
                $kaspi = array_merge($kaspi35, $kaspi42);

                if(in_array($request->user_id, $kaspi)) {
                    $fine = UserFine::where('user_id', $request->user_id)
                    ->whereDate('day', $date->format('Y-m-d'))
                    ->where('fine_id', 53)
                    ->first();

                    if($fine) {
                        $fine->status = 1;
                        $fine->save();
                    } else {
                        $userFine = new UserFine;
                        $userFine->user_id = $request->user_id;
                        $userFine->fine_id = 53;
                        $userFine->status = 1;
                        $userFine->day = $date;
                        $userFine->note = '';
                        $userFine->save();
                    }
                }
                
            }
            

        }


        /**
         *  // Подключился позже 
         */
        if ($request->type == 7) { 
            
            $trainee = UserDescription::where('is_trainee', 1)->where('user_id', $request->user_id)->first();
            
            if($trainee) {
                
                $editPersonLink = 'https://admin.u-marketing.org/timetracking/edit-person?id=' . $request->user_id;
                $recruiters = User::where('position_id', 46)->get();
                
                // Поиск ID лида или сделки
                if($trainee->lead_id != 0) {
                    $lead_id = $trainee->lead_id;
                } else {
                    $lead = Lead::where('phone', $targetUser->phone)->orderBy('id', 'desc')->first();
                    if($lead) {
                        $lead_id = $lead->lead_id;
                    } else {
                        $lead_id = 0;
                    }
                }

                $lead = Lead::where('user_id', $request->user_id)->first();
                if($lead) {
                    $lead->status = 'TRAINING';
                    $lead->save();
                }
                //
                $nootis = UserNotification::where([
                    'title' => 'Пропал с обучения',
                    'about_id' => $targetUser->id,
                ])->get();

                foreach($nootis as $noti) {
                    $noti->read_at = now();
                    $noti->save();
                }
              

                ///////// // перенос сделки с  Пропал с обучения на Обучается в БИТРИКС

                $bitrix = new Bitrix();

                if($trainee->deal_id != 0) {
                    $deal_id = $trainee->deal_id;
                } else if($lead_id != 0) {
                    $deal_id = $bitrix->findDeal($lead_id, false);
                    usleep(1000000); // 1 sec
                } else {
                    $deal_id = 0;
                }  
                
                if($deal_id != 0) {
                    $bitrix->changeDeal($deal_id, [
                        'STAGE_ID' => 'C4:18' 
                    ]);
                }

                /////-*-*-*-----------*-*-*-*-*-*-*//
                


            }
        }
        

        if($request->type == 7) {
            $up = UserPresence::where('date', $date)
                ->where('user_id', $request->user_id)
                ->first();
            if($up) $up->delete();
            UserPresence::create(['user_id' => $request->user_id, 'date' => $date]);
        }

        if($request->type == 2) {
            $up = UserPresence::where('date', $date)
                ->where('user_id', $request->user_id)
                ->first();
            if($up) $up->delete();
        }


        /**
         * Тип уволить с отработкой и без
         */

        if ($request->type == 4) { // Уволенный сотрудник DayType::DAY_TYPES['ABCENSE']
            $trainee = UserDescription::where('is_trainee', 1)->where('user_id', $request->user_id)->first();
            if($trainee) {

                // Поиск ID лида или сделки
                if($trainee->lead_id != 0) {
                    $lead_id = $trainee->lead_id;
                } else {
                    $lead = Lead::where('phone', $targetUser->phone)->orderBy('id', 'desc')->first();
                    if($lead) {
                        $lead_id = $lead->lead_id;
                    } else {
                        $lead_id = 0;
                    }
                }

                ///////// // перенос сделки в статус Отказ от Вакансии

                $bitrix = new Bitrix();

                if($trainee->deal_id != 0) {
                    $deal_id = $trainee->deal_id;
                } else if($lead_id != 0) {
                    $deal_id = $bitrix->findDeal($lead_id, false);
                    usleep(1000000); // 1 sec
                } else {
                    $deal_id = 0;
                }  
                
                if($deal_id != 0) {
                    $bitrix->changeDeal($deal_id, [
                        'STAGE_ID' => 'C4:LOSE' 
                    ]);
                }

                //
                $trainee->fired = now();
                $trainee->save();

                UserDescription::make([
                    'user_id' => $request->user_id,
                    'fired' => now(),
                ]);

                ////////////
                User::deleteUser($request);
            } else {
                
                if($request->fire_type == 1) { // Без отработки
                    User::deleteUser($request);
                } else { // C отработкой
                    if ($request->hasFile('file')) { // Заявление об увольнении
                        $file = $request->file('file');
                        $resignation = $targetUser->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                        $file->move("static/profiles/" . $targetUser->id . "/resignation", $resignation);
        
                        $downloads = Downloads::where('user_id', $targetUser->id)->first();
                        if ($downloads) {
                            $downloads->resignation = $resignation;
                            $downloads->save();
                        } else {
                            $downloads = Downloads::create([
                                'user_id' => $targetUser->id,
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
                   User::deleteUser($request);
                }
            }

           
            if(isset($request->comment)) {
                
                if($trainee) {
                    // Причина отсутствия 1 2 3 дни
                    $type = UserAbsenceCause::THIRD_DAY;

                    $lead = Lead::where('user_id', $request->user_id)->first();

                    if($lead) {
                        if($date->format('Y-m-d') == Carbon::parse($lead->invite_at)->format('Y-m-d')) {
                            $type = UserAbsenceCause::FIRST_DAY;
                        } else if($date->format('Y-m-d') == Carbon::parse($lead->day_second)->format('Y-m-d')) {
                            $type = UserAbsenceCause::SECOND_DAY;
                        }
                    }

                    UserAbsenceCause::createOrUpdate([
                        'user_id' => $request->user_id,
                        'date' => $date->day(1)->format('Y-m-d'),
                        'type' => $type,
                        'text' => $request->comment,
                    ]);
                } 

                $ud = UserDescription::where('user_id', $request->user_id)->first();
                if($ud) {
                    $ud->fire_cause = $request->comment;
                    $ud->fire_date = now();
                    $ud->save();
                } else {
                    UserDescription::create([
                        'user_id' => $request->user_id,
                        'fire_cause' => $request->comment,
                        'fire_date' => now(),
                    ]);
                }
                
            }
        }

        /////////
        return ['success' => 1, 'history' => $history, 'type' => $daytype ? $daytype->type : 0];
    }

    public function getTotalsOfReports(Request $request) {
        
        
        $group = ProfileGroup::find($request->group_id);
        if (!empty($group) && $group->users != null) {
            $x_users = json_decode($group->users);
        }
        
        $users_ids = array_unique($x_users);
        
        $sum = Timetracking::getSumHoursPerMonthByUsersIds($users_ids, $request->month, $request->year);
        
        foreach ($sum as $key => $value) {
            $sum[$key] = number_format((float)$value / 9, 2, '.', '');
        }
        
        return response()->json([
            'sum' => $sum 
        ]);
    }

    public function getUserNotifications(Request $request) {
      
        $user = User::withTrashed()
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where('ud.notifications', '!=', '[]')
            ->where('users.id', $request->user_id)
            ->select(DB::raw("CONCAT_WS(' ',users.ID, users.last_name, users.name) as name"), 'users.ID as id', 'ud.notifications as notifications')
            ->first();
    
        $notifications = [];
        
        if($user) {
            foreach(json_decode($user->notifications) as $noti) {
                
                $template = NotificationTemplate::where('id', $noti[0])
                    ->select('id','title', 'message', 'need_group')
                    ->first();

                if($template) {
                    $_groups = [];

                    $noti[0] = [
                        'id' => $template->id,
                        'title' => $template->title,
                    ];
                    foreach($noti[1] as $group_id) {
                        $group = ProfileGroup::find($group_id);

                        if($group) {
                            array_push($_groups, [
                                'id' => $group->id, 
                                'name' => $group->name
                            ]);
                        }
                    }
                    $noti[1] = $_groups;
                    $noti[2] = $template->need_group;

                    array_push($notifications, $noti);
                }
            }   
        }

        return response()->json([
            'notifications' => $notifications,
        ]);
    }

    public function saveUserNotifications(Request $request) {

        $array = [];

        $left_noti = [];

        foreach($request->noti as $noti) {
            try {
                $item = [];
                $item[0] = $noti[0]['id'];    
                $item[1] = [];    
                
                foreach($noti[1] as $group) {
                    array_push($item[1], $group['id']);
                }
                
                array_push($array, $item);   
                array_push($left_noti, $noti[0]['id']);

                $template = NotificationTemplate::find($noti[0]['id']);
                if($template) {
                    $ids = json_decode($template->ids);
                    array_push($ids, $request->user_id);
                    $ids = array_unique($ids);
                    $template->ids = json_encode($ids);
                    $template->save();
                }

            } catch(\Exception $e) {
                continue;
            }
        }

        $ud = UserDescription::where('user_id', $request->user_id)->first();

        if($ud) {
            $notifications = $ud->notifications;

            foreach($notifications as $noti) {
                if(!in_array($noti[0], $left_noti)) {
                    $template = NotificationTemplate::find($noti[0]);
                    if($template) {

                        $ids = json_decode($template->ids);
                        $ids = array_diff($ids, [$request->user_id]);
                        $ids = array_values($ids);
                        $template->ids = json_encode($ids);
                        $template->save();
                    }
                }
            }
            

            $ud->notifications = $array;
            $ud->save();
        } else {
            UserDescription::create([
                'user_id' => $request->user_id,
                'notifications' =>  $array
            ]);
        }

        
    }

    public function getTimeAddresses(Request $request)
    {
        $group = ProfileGroup::where('name', $request->group_id)
            ->first();

        $time_variants = [
            '-1' => 'Из U-calls',
            '0' => 'Из табеля',
        ];
        $time_exceptions_options = [];
        $time_exceptions = [];

        if($group) {
            $activities = Activity::where('group_id', $group->id)->get();
            foreach ($activities as $key => $activity) {
                $time_variants[$activity->id] = $activity->name;
            }

            if($group->users != null) {
                $time_exceptions_options = User::leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                    ->where('ud.is_trainee', 0)
                    ->where('UF_ADMIN', 1)
                    ->whereIn('users.id', json_decode($group->users)) 
                    ->get(['users.id', DB::raw("CONCAT(name,' ',last_name,'-',email) as email")]);
            }

            $time_exceptions = User::leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('ud.is_trainee', 0)
                ->where('UF_ADMIN', 1)
                ->whereIn('users.id', $group->time_exceptions) 
                ->get(['users.id', DB::raw("CONCAT(name,' ',last_name,'-',email) as email")]);
        } 
        
        
        return [
            'time_variants' => $time_variants,
            'time_exceptions_options' => $time_exceptions_options,
            'time_exceptions' => $time_exceptions,
        ];
    }

    public function saveTimeAddresses(Request $request)
    {
        $group = ProfileGroup::where('name', $request->group_id)
            ->first();

        if($group) {
            $group->time_address = $request->time_address;

            $users = [];
            foreach ($request->time_exceptions as $key => $te) {
                array_push($users, $te['id']);
            }
            $group->time_exceptions = $users;
            $group->save();
        }
    }

    public function restoreGroup(Request $request)
    {   
        $group = ProfileGroup::where('name', 'like', '%' . $request->id . '%')->first();
       
        if($group) {
            $group->active = 1;
            $group->save();
        }
    }
    
}
