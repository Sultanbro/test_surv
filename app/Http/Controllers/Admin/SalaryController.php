<?php

namespace App\Http\Controllers\Admin;

use App\Components\TelegramBot;
use App\Fine;
use App\Exam;
use App\DayType;
use App\Http\Controllers\Controller;
use App\ProfileGroup;
use App\ProfileGroupUser;
use App\Salary; 
use App\GroupSalary; 
use App\Kpi;
use App\Trainee;
use App\Timetracking;
use App\TimetrackingHistory;
use App\AnalyticsSettingsIndividually;
use App\User;
use App\UserFine;
use App\SalaryApproval;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use App\Classes\Helpers\Phone;
use Artisan;
use App\UserNotification;
use App\UserDescription;
use App\Models\User\Card;
use App\Models\TestBonus;
use App\Classes\Helpers\Currency;
use App\Models\Admin\ObtainedBonus;
use App\Models\Admin\EditedKpi;
use App\Models\Admin\EditedBonus;
use App\Models\Admin\EditedSalary;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Imports\UsersImport;
use App\Service\Department\UserService;

class SalaryController extends Controller
{
    
    public function __construct()
    {
        View::share('title', 'Начисления');
        View::share('menu', 'timetrackingaccruals');
        $this->middleware('auth');
    }

    public function index()
    {
        if(!auth()->user()->can('salaries_view')) {
            return redirect('/');
        }

        $groups = ProfileGroup::where('active', 1);

        if(!auth()->user()->is_admin) $groups->whereIn('id', auth()->user()->groups);
            
        
        $groups = $groups->get();

        //if(auth()->id() == 4444) dd($groups);
        

        $date = Carbon::now()->day(1)->format("Y-m-d");
        

        if(auth()->user()->is_admin != 1) {
            $_groups = [];
            foreach ($groups as $key => $group) {
    
                if(!in_array(auth()->id(), json_decode($group->editors_id))) continue;
    
                $approval = SalaryApproval::where('group_id', $group->id)->where('date', $date)->first();
                if($approval) {
                    $user = User::withTrashed()->find($approval->user_id);
                    $group->salary_approved_by = $user ? $user->last_name . ' ' . $user->name : $approval->user_id;
                    $group->salary_approved_date = Carbon::parse($approval->updated_at)->format('H:i d.m.Y');
                    $group->salary_approved = 1;
                } else {
                    $group->salary_approved = 0;
                }
    
                $_groups[] = $group;
            }
           
            $groups = $_groups;
        }   
       
       
        $years = ['2020', '2021', '2022']; // TODO Временно. Нужно выяснить из какой таблицы брать динамические годы

        return view('admin.salary', compact('groups', 'years'));
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

    /**
     * Страница начисления
     */
    public function salaries(Request $request)
    {       
        if(!auth()->user()->can('salaries_view')) {
            return [
                'error' => 'access'
            ];
        }
        
        $year  = $request->year;
        $month = $request->month;

        $currentUser = User::bitrixUser();

        $date = Carbon::createFromDate($year, $month, 1);

        $data = [];

        if ($request->has('group_id')) {
            $group = ProfileGroup::find($request->group_id);
            
            // $users_ids = [];
            // if($group) $users_ids = json_decode($group->users, true);

            if($request->user_types == 0) {
                $users = (new UserService)->getEmployees($request->group_id, $date->format('Y-m-d')); 
            }

            if($request->user_types == 1) {
                $users = (new UserService)->getFiredEmployees($request->group_id, $date->format('Y-m-d')); 
            }
    
            if($request->user_types == 2) {
                $users = (new UserService)->getTrainees($request->group_id, $date->format('Y-m-d')); 
            }

            $users_ids = collect($users)->pluck('id')->toArray();

            // if($request->user_types == 2) 
        }

        $group_editors = is_array(json_decode($group->editors_id))
            ? json_decode($group->editors_id)
            : [];

        // Доступ к группе
        if(auth()->user()->is_admin == 1) {
            
        } else if (!in_array($currentUser->id, $group_editors)) {
            return [
                'error' => 'access',
            ];
        }

        //////////////////////

        $data['users'] = [];
        $data['total_resources'] = 0;

        $date = Carbon::createFromDate($request->year, $request->month, 1);

        $arr = Salary::salariesTable($request->user_types, $date->format('Y-m-d'), $users_ids, $request->group_id);
     
        $data['users'] = $arr['users'];
        $data['total_resources'] = $arr['total_resources'];
        $data['auth_token'] = Auth::user()->remember_token;
        $data['year'] = $request['year'];
    

        // total on group

        $group = ProfileGroup::find($request->group_id);

        $sdate = Carbon::createFromDate($request->year, $request->month, 1)->format('Y-m-d');
        $users_ids = json_decode($group->users);
        $data['group_total'] = GroupSalary::where('group_id', $request->group_id)
            ->where('date', $sdate)
            ->where('type', 1)
            ->get()->sum('total');
        
        // group fired 

        $data['group_fired'] = GroupSalary::where('group_id', $request->group_id)
            ->where('date', $sdate)
            ->where('type', 2)
            ->get()
            ->sum('total');

        $data['accruals'] = GroupSalary::getAccruals($sdate);

        if(Auth::user()->is_admin == 1) {
            $data['all_total'] = GroupSalary::where('date', $sdate)
                ->where('type', 1)
                ->whereNotIn('group_id', [34])
                ->get()->sum('total');

            $data['all_total_fired'] = GroupSalary::where('date', $sdate)
                ->where('type', 2)
                ->whereNotIn('group_id', [34])
                ->get()->sum('total');
        } else {
            $data['all_total'] = 0;
            $data['all_total_fired'] = 0;
        }
        
        //////
        $groups = ProfileGroup::where('active', 1)->get();

        $salary_approved = []; // костыль

        $_groups = [];
        
        $currentGroup = null;

        $data['salary_approved'] = 0;
        
        foreach ($groups as $key => $group) {

            $editors_id = json_decode($group->editors_id);
            if ($group->editors_id == null) $editors_id = [];

            if(auth()->user()->is_admin != 1 && !in_array(auth()->id(), $editors_id)) continue;
            
            $approval = SalaryApproval::where('group_id', $group->id)->where('date', $sdate)->first();

            if($approval) {
                $xuser = User::withTrashed()->where('id', ($approval->user_id))->first();
                $group->salary_approved_by = $xuser ? $xuser->last_name . ' ' . $xuser->name : $approval->user_id;
                $group->salary_approved_date = Carbon::parse($approval->updated_at)->format('H:i d.m.Y');
                $group->salary_approved = 1;

                if($group->id == $request->group_id) {
                    $currentGroup = $group;
                }
            } else {
                $group->salary_approved = 0;
                if($group->id == $request->group_id) {
                    $currentGroup = $group;
                }
            }

            $_groups[] = $group;
        }

        $data['groups'] = $_groups;
        $data['currentGroup'] = $currentGroup;
    
        return $data;
    }

    public function recalc(Request $request)
    {
        $period = $request->period;
        $user_id = $request->user_id;
        $date = $request->group_id;
        
        Artisan::call('salary:update', [
            'date' => $request->date,
            'group_id' => $request->group_id,
            'type' => 'day',
            'user_id' => $user_id,
            'filter' => 'working',
        ]);

        return [
            'code' => 200,
            'message' => 'Успешно'
        ];
    }

    public function update(Request $request)
    {
        $day = $request->day;
        $year = $request->year;
        $type = $request->type;
        $month = $request->month;
        $amount = $request->amount;
        $comment = $request->comment;
        $user_id = $request->user_id;

        $date  = Carbon::createFromDate($year, $month, $day)->format('Y-m-d');

        $salary = Salary::where('user_id', $user_id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->whereDay('date', $day)
            ->first();    

        if($salary) {
            if($type == 'avans') {
                $salary->comment_paid = $comment;
                $salary->paid = $amount;
            }

            if($type == 'bonus') {
                $salary->comment_bonus = $comment;
                $salary->bonus = $amount;    
            }

            $salary->save();
        } else {
            if($type == 'avans') {
                Salary::create([
                    'user_id' => $user_id,
                    'date' => $date,
                    'amount' => 0,
                    'comment_paid' => $comment,
                    'paid' => $amount,
                ]);
            }

            if($type == 'bonus') {
                Salary::create([
                    'user_id' => $user_id,
                    'date' => $date,
                    'amount' => 0,
                    'comment_bonus' => $comment,
                    'bonus' => $amount,
                ]);
            }
            
        }

        if($type == 'avans' || $type == 'bonus') {
            if($type == 'avans') $text = 'аванс';
            if($type == 'bonus') $text = 'бонус';

            $author = Auth::user()->last_name . ' ' . Auth::user()->name;
            UserNotification::create([
                'user_id' => $user_id,
                'about_id' => $user_id,
                'title' => 'Добавлен ' . $text,
                'group' => now(),
                'message' => $author . ': '.$comment
            ]);
        }

        if($type == 'avans') $type = 'аванс';
        if($type == 'bonus') $type = 'бонус';

        $editor = Auth::user();
        $history = TimetrackingHistory::create([
            'user_id' => $user_id,
            'author_id' => $editor->id,
            'author' => $editor->last_name . ' ' . $editor->name,
            'date' => $date,
            'description' => 'Добавлен <b>' . $type . '</b> на сумму ' . $amount . '<br> Комментарии: ' . $comment
        ]);

        return json_encode([
            'author' => Auth::user()->last_name . ' '.  Auth::user()->name,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'description' => 'Добавлен <b>' . $type . '</b> на сумму ' . $amount . '<br> Комментарии: ' . $comment,
            'day' => $request->day,
            'date' => $date,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $rules = [
            'year' => 'required',
            'month' => 'required',
            'group_id' => 'required',
        ];

        $currentUser = User::bitrixUser();

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->to('/timetracking/salaries')->withErrors('Поля не введены');
        }

        $group = ProfileGroup::find($request->group_id);
        $date = Carbon::createFromDate($request->year, $request->month, 1);

        $users = (new UserService)->getUsers($request->group_id, $date->format('Y-m-d')); 
        $users_ids = collect($users)->pluck('id')->toArray();

        // $users_ids = [];
        // if (!empty($group) && $group->users != null) {
        //     // $users_ids = json_decode($group->users);
        //     if($group) $users_ids = json_decode($group->users, true);
        // }
  
        $group_editors = is_array(json_decode($group->editors_id)) ? json_decode($group->editors_id) : [];
        
        // Доступ к группе
        if(auth()->user()->is_admin != 1) {
            if (!in_array($currentUser->id, $group_editors)) {
                return [
                    'error' => 'access',
                ];
            }
        }
        
        $date = $request->year . '-' . $request->month . '-01';
     
        $working_users = DB::table('users')
            ->leftJoin('user_descriptions', 'user_descriptions.user_id', '=', 'users.id')
            ->whereNull('users.deleted_at')
            ->where('is_trainee', 0)
            ->whereIn('users.id', $users_ids);

        /////////////

        // $x_users = User::withTrashed()
        //     ->whereDate('deleted_at', '>=', Carbon::createFromDate($request->year, $request->month, 1)->format('Y-m-d'))
        //     ->get(['id','last_group']);

        // $fired_users = [];
        // foreach($x_users as $d_user) {
        //     if($d_user->last_group) {
        //         $lg = json_decode($d_user->last_group);
        //         if(in_array($request['group_id'], $lg)) {
        //             array_push($fired_users, $d_user->id);
        //         }
        //     } 
        // }
        // $salary_users = Salary::whereYear('date', $request->year)
        //     ->whereMonth('date', $request->month)
        //     ->whereIn('user_id', $fired_users)
        //     ->get(['user_id'])
        //     ->pluck('user_id')
        //     ->toArray(); 
       
        // $fired_users_2 = array_unique($salary_users);

        // $fired_users = array_merge($fired_users, $fired_users_2);

        // $fired_users = array_unique(array_values($fired_users));

        ///////////
        $working_users = $working_users->get(['users.id'])->pluck('id')->toArray();
        $headings = [
            'ФИО', // 0
            'На карте', // 1
            'Возраст', // 2
            'Телефон', // 3
            'Карта', // 4
            'Отр. дни', // 5
            'Раб. дни', // 6
            'Ставка',  // 7
            'Начислено', // 9
            'KPI', // 10
            'Стажировочные', // 8
            'Бонус', // 11
            'ИТОГО', // 12
            'Авансы', // 13 
            'Штрафы', // 14 
            'ОПВ', // 15
            'ИПН', // 16
            'СО + СН', // 17
            'ИТОГО расход', // 18
            'К выдаче', // 19
            'В валюте' // 20
        ];

        $data = [];

        $date = Carbon::createFromDate($request->year,$request->month,1);
        
        $fired_users = (new UserService)->getFiredEmployees($request->group_id, $date->format('Y-m-d'));
        $fired_users = collect($fired_users)->pluck('id')->toArray();

        $working_users = $this->getSheet($working_users, $date, $request->group_id);
        $fired_users = $this->getSheet($fired_users, $date, $request->group_id);
    
        $_users = array_merge([['']],$working_users['users']);
        $_users = array_merge($_users, [[''],[''],['']]);
        $_users = array_merge($_users, $fired_users['users']);
     
        $data[0] = [
            'name'     => 'Действующие и Уволенные',
            'sheet'    => $_users,
            'headings' => $headings,
            'counter'  => count($working_users['users']) - 1
        ];

        if(ob_get_length() > 0) ob_clean(); //  ob_end_clean();
        $edate = $date->format('m.Y');

        $exp = new \App\Exports\UsersExport($data[0]['name'], $data[0]['headings'],$data[0]['sheet'], $group ,$data[0]['counter']);
        $exp_title = 'Начисления ' . $edate .' "'.$group->name . '".xlsx';

        return Excel::download($exp, $exp_title);
    }

    private function getSheet($users_ids, $date, $group_id) {
        
        $users = \DB::table('users')
            ->join('working_times as wt', 'wt.id', '=', 'users.working_time_id')
            ->join('working_days as wd', 'wd.id', '=', 'users.working_day_id')
            ->join('zarplata as z', 'z.user_id', '=', 'users.id')
            ->leftJoin('timetracking as t', 't.user_id', '=', 'users.id')
            ->whereIn('users.id', array_unique($users_ids))
            ->selectRaw("users.id as id,
                        users.phone as phone,
                        users.program_id as program_id,
                        CONCAT(users.last_name,' ',users.name) as full_name,
                        users.working_time_id as working_time_id,
                        users.working_day_id as working_day_id,
                        users.birthday as birthday,
                        wd.name as workDay,
                        wt.time as workTime,
                        z.zarplata as salary,
                        z.card_kaspi as card_kaspi,
                        z.kaspi_cardholder as kaspi_cardholder,
                        z.jysan_cardholder as jysan_cardholder,
                        z.card_jysan as card_jysan,
                        z.kaspi as kaspi,
                        z.jysan as jysan,
                        users.currency as currency,
                        CONCAT('KASPI', '') as card
                        ")
            ->groupBy('id', 'phone', 'full_name', 'workDay', 'working_time_id', 'workTime', 'salary', 
            'card_kaspi', 'card_jysan', 'jysan', 'kaspi','kaspi_cardholder','jysan_cardholder', 'card', 'program_id', 'birthday','currency', 'working_day_id')
            ->get();

            //if($users->where('id', 14073)->first()) dd($users_ids);
       //     if($user->id == 14073) dd($users_ids);
        
        $fines = Fine::pluck('penalty_amount', 'id')->toArray();
        $data = [];

        $allTotal = [
            0 => '',
            1 => '',
            2 => 0,
            3 => '',
            4 => '',
            5 => 0,
            6 => 0,
            7 => 0,
            8 => 0,
            9 => 0,
            10 => 0,
            11 => 0,
            12 => 0,
            13 => 0,
            14 => 0,
            15 => 0,
            16 => 0,
            17 => 0,
            18 => 0,
            19 => 0,
        ];
        $i = 0;
        
        $data['users'] = [];
        
        foreach ($users as $user) { /** @var User $user */

            $_user = User::withTrashed()->find($user->id);
            $ugroups = $_user->inGroups();

            if(count($ugroups) > 0) {
                if($ugroups[0]->id != $group_id) {
                    continue;
                }
            }
           
           

            // Вычисление даты принятия
            $user_applied_at = $_user->applied_at();

            $ud = UserDescription::where('user_id', $user->id)->first();

            if($ud && $ud->is_trainee != 0) {
                continue;
            } 
            // delete not applied 
       
            
            // Суммы на месяц 
            $month_salary = Salary::where('user_id', $user->id)
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->select([
                    DB::raw('sum(paid) as avans'),
                    DB::raw('sum(bonus) as bonus'),
                ])
                ->first();
            
            // edited salary 

            $edited_salary =  EditedSalary::where('user_id', $user->id)
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->first();
            $edited_salary_amount = $edited_salary ? $edited_salary->amount : 0;

            // Почасовая оплата
            $hourly_pay = $_user->hourly_pay($date->format('Y-m-d'));
            
            // Карты и держатели карт
            $cards = '';
            $cardholder = '';
            if($user->kaspi) {
                $cardholder = $user->kaspi_cardholder;
                $cards = 'KASPI: ' . $this->phone_space($user->kaspi) . '; ' . $this->card_space($user->card_kaspi);
            } else if($user->card_kaspi) {
                $cardholder = $user->kaspi_cardholder;
                $cards = 'KASPI: ' . $this->card_space($user->card_kaspi);
            } else if($user->jysan) {
                $cardholder = $user->jysan_cardholder;
                $cards = 'JYSAN: ' . $this->phone_space($user->jysan) . '; ' . $this->card_space($user->card_jysan);
            } else if($user->card_jysan) {
                $cardholder = $user->jysan_cardholder;
                $cards = 'JYSAN: ' . $this->card_space($user->card_jysan);
            } else {
                $user_card = Card::where('user_id', $user->id)->first();
                if($user_card) {
                    $cardholder = $user_card->cardholder;
                    $_card = ($user_card->number) ? $this->card_space($user_card->number) . '; ' . $this->phone_space($user_card->phone) : $this->phone_space($user_card->phone);
                    $cards = $user_card->bank . '(' . $user_card->country . '): '. $_card;
                }
            }

            // рабочие дни
            $ignore = $user->working_day_id == 1 ? [6,0] : [0]; // Какие дни не учитывать в месяце
            $workDays = workdays($date->year, $date->month, $ignore);
                
            if($group_id == 53 && $date->year == 2022 && $date->month == 3) {
                $workdays = 19;
            } else if($group_id == 57  && $date->year == 2022 && $date->month == 3) {
                $workdays = 22;
            } else {
                $workdays = workdays($date->year, $date->month, $ignore);
            }

            if(!$edited_salary) $allTotal[6] += intval($workDays);

            // стажировочные    

            $trainee_days = DayType::select([
                    DB::raw('DAY(date) as day')
                ])
                ->where('user_id', $user->id)
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
               //->whereDate('date', '<', Carbon::parse($user_applied_at)->format('Y-m-d'))
                ->whereIn('type', [5,6,7])
                ->get(['day'])
                ->pluck('day')
                ->toArray();
            
            $trainee_days_count = count($trainee_days);
            
          

            // отработанные дни
            $yearAndMonth = $date->year . "-" . $date->month;
            $workedHours = Timetracking::select([
                    'total_hours', 
                    DB::raw('DAY(enter) as day')
                ])
                ->whereYear('enter', $date->year)
                ->whereMonth('enter', $date->month)
                //->whereNotIn('day', $trainee_days)
                ->where('user_id', $user->id)
                ->get();
            
            
            $workedHours = $workedHours->whereNotIn('day', $trainee_days); 
            
            

            //$workedHours = $workedHours->sum('total_hours');
            $workedHours = $workedHours->sum('total_hours') / 60;
            
            
            
           // dump($user->workTime);   
           //dump($workedHours, $user->workTime);  
        
            $workedDays = round($workedHours / $user->workTime, 2);
           // dump($workedDays);  
                // if($user->id == 10242) {
                //     dump($user_applied_at);
                //     dump($workedHours);
                //     //dump($workedDays);
                //     dump($hourly_pay);
                // }
                
            if(!$edited_salary) $allTotal[5] += $workedDays;
            
            // проверка сданных экзаменов  
            $wage = $user->salary; // WAGE: оклад + бонус от экзамена
            $bonusFromExam = 0; // бонус от экзамена
            $exam = Exam::where('user_id', $user->id) // Проверка сдавал ли сотрудник книгу в этом месяце
                ->where('month', $date->month)
                ->where('year', $date->year)
                ->first();
            
                   

            if(!is_null($exam) && $exam->success == 1) {
                $bonusFromExam = 10000;
                $wage += $bonusFromExam;
            }      

            // ставка
            $allTotal[7] += intval($wage) ?? 0;
            
            
            
           

            // if($user->id == 9975) {
            //     dd($trainee_days);
            // }

            // начислено
            //$salary = round($workedHours * $hourly_pay, 2);

            $salary_table = Salary::salariesTable(-1, $date->format('Y-m-d'), [$user->id]);
            
            // if($user->id == 5670) dump($salary_table);


            $salary = 0;
            $trainee_fees = 0;
           
            
            if(count($salary_table['users']) > 0) {
                $arrs = $salary_table['users'][0];
                for($i =1;$i<=$date->daysInMonth;$i++) {
                    if($arrs->trainings[$i]) {
                        $trainee_fees += $arrs->earnings[$i] ?? 0;
                    } else {
                        $salary += $arrs->earnings[$i] ?? 0;
                        
                    }
                }   
            }
           
           

            // if($user->id == 10230) {
            //         dump($workedHours);
            //         dump($hourly_pay);
            //         dd($salary);
            // } 

            // if($user->id == 11041) {
            //     dump($trainee_days_before_apply_count);
            //     dump($workedHours);
            //     dump($hourly_pay);
            //     dd($salary);

            // }

            if(!$edited_salary) $allTotal[8] += $salary;

            // $trainee_fees = round($trainee_days_count * $hourly_pay * $user->workTime * $user->internshipPayRate(), 2); // стажировочные на пол суммы
            // //$trainee_fees = 0;
            if(!$edited_salary) $allTotal[10] += $trainee_fees;

            // KPI 

            $editedKpi = EditedKpi::where('user_id', $user->id)
                    ->whereYear('date', $date->year)
                    ->whereMonth('date', $date->month)
                    ->first();

            if($editedKpi) {
                $kpi = $editedKpi->amount;
            } else {
                $kpi = Kpi::userKpi($user->id, $date->format('Y-m-d'));
            }   

            if(!$edited_salary) $allTotal[9] += $kpi;

            // Бонусы 

            $editedBonus = EditedBonus::where('user_id', $user->id)
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->first();

            if($editedBonus) {
                $bonus = $editedBonus->amount;
            } else {
                $ObtainedBonus = ObtainedBonus::onMonth($user->id, $date->format('Y-m-d'));

                $test_bonuses = TestBonus::selectRaw('sum(ROUND(amount,0)) as total')
                    ->where('user_id', $user->id)
                    ->whereYear('date', $date->year)
                    ->whereMonth('date', $date->month)
                    ->first('total')
                    ->total;
                    
                $bonus = round($month_salary->bonus + $ObtainedBonus + $test_bonuses);
            }  
            
            if(!$edited_salary) $allTotal[11] += $bonus;
              
            // ИТОГО доход
            $total_income = round($salary + $bonus + $kpi + $trainee_fees, 0);
            if(!$edited_salary) $allTotal[12] += $total_income;

            // Авансы

                    // headphone price minus
                    $headphones_amount = 0;
                    if($ud) {
                        $headphones_date = Carbon::parse($ud->headphones_date);
                        if($ud->headphones_amount > 0 &&
                            $headphones_date->year == $date->year &&
                            $headphones_date->month == $date->month) {
                            $headphones_amount = $ud->headphones_amount;
                        }

                    }
                    
            $prepaid = round($month_salary->avans) + $headphones_amount;
            if(!$edited_salary) $allTotal[13] += $prepaid;
            
            
                    
            
            // Не показывать если все по нулям
            
            if($edited_salary && $edited_salary->amount == 0) {
                continue;
            }
                
            // if($user->id == 5670) {

            
            // dump($salary);
            // dump($bonus);
            // dump($prepaid);
            // dump($trainee_fees);
            // dump($edited_salary_amount);
            // dd($user->id);
            // }
            if($salary == 0 && $bonus == 0 && $prepaid == 0 && $trainee_fees == 0 && $edited_salary_amount == 0) {
                continue;
            }
           
            // if($user->id == 10242) {
            //     dd($salary);
            // }

            // Штрафы
            $penalty = 0;
            $userFines = UserFine::whereYear('day', $date->year)
                ->whereMonth('day', $date->month)
                ->where('user_id', $user->id)
                ->where('status', UserFine::STATUS_ACTIVE)
                ->get();

            foreach($userFines as $fine) {
                $penalty += $fines[$fine->fine_id];
            }
              
            if(!$edited_salary) $allTotal[14] += $penalty;

            
            // Итого расход
            $expense = $prepaid + $penalty;
            if(!$edited_salary)  $allTotal[18] += $expense;

            // К выдаче
            $total_payment = round($total_income - $expense);
            if(!$edited_salary) $allTotal[19] += $total_payment >= 0 ? $total_payment : 0;
            
            // В валюте
            $currency_rate = in_array($user->currency, array_keys(Currency::rates())) ? (float)Currency::rates()[$user->currency] : 0.0000001;

            $on_currency = number_format((float)$total_payment * (float)$currency_rate, 0, '.', '') . strtoupper($user->currency);

            

            // Строка в экселе
            try {
                if($edited_salary) {
                    $allTotal[8] += (float)$edited_salary->amount;
                    $on_currency = number_format((float)$edited_salary->amount * (float)$currency_rate, 0, '.', '') . strtoupper($user->currency);
                    $data['users'][] = [
                        0 => $user->full_name, // Действующие
                        1 => $cardholder, // Card Holder name
                        2 => $user->birthday ? floor((strtotime(now()) - strtotime($user->birthday)) / 3600 / 24 / 365) : '', // Возраст
                        3 => $user->phone ? ' +' . Phone::normalize($user->phone) : '', 
                        4 => $cards, // Номер карты 
                        5 => $workedDays, // отработанные дни
                        6 => $workDays, // рабочие дни
                        7 => $wage ?? 0, // ставка
                        8 => (int)$edited_salary->amount, // начислено 
                        9 => 0, // KPI
                        10 => 0, // стажировочные
                        11 => 0, //  7 => $user->salary ?? 0, // оклад //8 => $bonusFromExam ?? 0, // ставка  
                        12 => (int)$edited_salary->amount, // ИТОГО доход, 
                        13 => 0, // Авансы
                        14 => 0, // Штрафы    //11 => $total, // Итого
                        15 => 0, // ОПВ
                        16 => 0, // ИПН
                        17 => 0, // СО + СН
                        18 => 0, // итого расход
                        19 => $this->space($edited_salary->amount, 3, true), // к выдаче
                        20 => $this->space($on_currency, 3, true), // в валюте
                    ];
                } else {
                  
                    $data['users'][] = [
                        0 => $user->full_name, // Действующие
                        1 => $cardholder, // Card Holder name
                        2 => $user->birthday ? floor((strtotime(now()) - strtotime($user->birthday)) / 3600 / 24 / 365) : '', // Возраст
                        3 => $user->phone ? ' +' . Phone::normalize($user->phone) : '', 
                        4 => $cards, // Номер карты 
                        5 => $workedDays, // отработанные дни
                        6 => $workDays, // рабочие дни
                        7 => $wage ?? 0, // ставка
                        8 => (int)$salary, // начислено 
                        9 => $kpi, // KPI
                        10 => $trainee_fees ?? 0, // стажировочные
                        11 => $bonus, //  7 => $user->salary ?? 0, // оклад //8 => $bonusFromExam ?? 0, // ставка  
                        12 => $total_income, // ИТОГО доход, 
                        13 => $prepaid, // Авансы
                        14 => $penalty, // Штрафы    //11 => $total, // Итого
                        15 => 0, // ОПВ
                        16 => 0, // ИПН
                        17 => 0, // СО + СН
                        18 => $expense, // итого расход
                        19 => $this->space($total_payment, 3, true), // к выдаче
                        20 => $this->space($on_currency, 3, true), // в валюте
                    ];
                }
            } catch (\Exception $e) {
                dd($e);
            }
        }

        // сортировка по имени
        $name_asc = array_column($data['users'], 0);
        array_multisort($name_asc, SORT_ASC, $data['users']); 

        // К выдаче сумма форматированная
        $allTotal[9] = $this->space(round($allTotal[9]), 3, true);
        $allTotal[19] = $this->space(round($allTotal[19]), 3, true);
        
        
        // Итоги в конце таблицы
        array_push($data['users'], $allTotal);

        return $data;
    }

    private function space($str, $step, $reverse = false) {
    
        if ($reverse)
            return strrev(chunk_split(strrev($str), $step, ' '));
        
        return chunk_split($str, $step, ' ');
    }

    private function phone_space($phone) {
        if($phone == null) return '';
        $string = preg_replace('/[^0-9]/', '', $phone);
		$str = substr($string, 0, 2);
		if(in_array($str, ['77', '87'])) $phone = $this->add_space($string, [1,4,7,9,11]);
		if(in_array($str, ['99','33','38'])) $phone = $this->add_space($string, [3]);
		return $phone;
	}
    
    private function card_space($card) {
        $card = preg_replace('/\s+/', '', $card);
        return $this->add_space($card, [4,8,12,16]);
    }

	private function add_space($str, array $spaces) {
		$result = '';
		for($i = 0; $i < strlen($str);$i++) {
			if(in_array($i, $spaces)) $result .= ' ';
			$result .= $str[$i];
		}
		return $result;
	}
    
    public static function convertCardNumberWithDots($card_number)
    {
        if (empty($card_number) || $card_number == '') return '';
        for ($i = 0; $i <= 12; $i += 4) $card_arr[] = substr($card_number, $i, 4);
        return implode('.', $card_arr);
    }
 
    public static function countDays($year, $month, $type)
    {
        $day_count = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $result = 0;
        for ($i = 1; $i <= $day_count; $i++) {

            $date = $year . '/' . $month . '/' . $i; //format date
            $get_name = date('l', strtotime($date)); //get week day
            $day_name = substr($get_name, 0, 3); // Trim day name to 3 chars

            //if not a weekend add day to array
            switch ($type) {
                case '6-1':
                    if ($day_name != 'Sun') {
                        $result += 1;
                    }
                    break;
                case '5-2':
                default:
                    if ($day_name != 'Sun' && $day_name != 'Sat') {
                        $result += 1;
                    }
                    break;
            }

        }
        return $result;
    }

    public static function getWorkingDays($workDayType, $month, $year)
    {
        $date = date();// Carbon::now()->setDate($year, $month, 1);
        switch ($workDayType) {
            case '6-1':
                $wd = $date;//Carbon::createFromDate($year, $month);
                break;
            case '5-2':
            default:
                $wd = 1;//Carbon::createFromDate($year, $month);
                break;
        }
        return $wd;
    }
    
    /**
     * approve salary was checked for previous month
     */
    public function approveSalary(Request $request) {
        $approval = SalaryApproval::where('group_id', $request->group_id)
            ->whereMonth('date', $request->month)
            ->whereYear('date', $request->year)
            ->first();

        if(!$approval) {
            SalaryApproval::create([
                'group_id' => $request->group_id,
                'date' => Carbon::createFromDate($request->year, $request->month, 1)->format('Y-m-d'),
                'user_id' => Auth::user()->id
            ]);
        } 
    }
    /**
     * Edit bonus or kpi
     */
    public function editPremium(Request $request) {

        $type = $request->type; // bonus or kpi
        $user_id = $request->user_id;
        $amount = $request->amount;
        $comment = $request->comment;
        $date = $request->date;

        if($type == 'kpi') {
            $edited = EditedKpi::where('user_id', $user_id)
                ->whereYear('date', Carbon::parse($date)->year)
                ->whereMonth('date', Carbon::parse($date)->month)
                ->first();
                
            if(!$edited) {
                $edited = new EditedKpi();
            } 

            $edited->user_id = $user_id;
            $edited->author_id = auth()->id();
            $edited->amount = $amount;
            $edited->comment = $comment;
            $edited->date = $date;
            $edited->final = 1;
            $edited->save();

        }

        if($type == 'bonus') {
            $edited = EditedBonus::where('user_id', $user_id)
                ->whereYear('date', Carbon::parse($date)->year)
                ->whereMonth('date', Carbon::parse($date)->month)
                ->first();
                
            if(!$edited) {
                $edited = new EditedBonus();
            } 

            $edited->user_id = $user_id;
            $edited->author_id = auth()->id();
            $edited->amount = $amount;
            $edited->comment = $comment;
            $edited->date = $date;
            $edited->save();
        }

        if($type == 'final') {
            $edited = EditedSalary::where('user_id', $user_id)
                ->whereYear('date', Carbon::parse($date)->year)
                ->whereMonth('date', Carbon::parse($date)->month)
                ->first();
                
            if(!$edited) {
                $edited = new EditedSalary();
            } 

            $edited->user_id = $user_id;
            $edited->author_id = auth()->id();
            $edited->amount = $amount;
            $edited->comment = $comment;
            $edited->date = $date;
            $edited->save();
        }

    }

    public function getTotal(Request $request, $user_ids)
    {
        $kpi_total = 0;
        foreach($user_ids as $user_id) { 
            $kpi_total += Kpi::userKpi($user_id, Carbon::createFromDate($request->year, $request->month, 1)->format('Y-m-d'));
        }

        $editedBonuses = EditedBonus::selectRaw('sum(ROUND(amount,0)) as total')
            ->whereYear('date', $request->year)
            ->whereMonth('date', $request->month)
            ->whereIn('user_id', $user_ids)
            ->first()
            ->total;
        
        $editedKpis = EditedKpi::selectRaw('sum(ROUND(amount,0)) as total')
            ->whereYear('date', $request->year)
            ->whereMonth('date', $request->month)
            ->whereIn('user_id', $user_ids)
            ->first()
            ->total;
        //////////////////



       

        $total = 0;
        $month = Carbon::createFromDate($request->year, $request->month, 1)->format('Y-m-d');
       
        /////// TTS 
        $tts = Timetracking::whereYear('enter', $request->year)
                ->select([
                    'user_id',
                    DB::raw('sum(total_hours) as total_hours')
                ])
                ->whereMonth('enter', $request->month)
                ->whereIn('user_id', $user_ids)
                ->groupBy('user_id')
                ->get();
     
        foreach($tts as $tt) {
 
           
            $user = User::withTrashed()->find($tt->user_id);
            if($user) {
                $hourly_pay = $user->hourly_pay($month);
                $total += $hourly_pay * $tt->total_hours / 60;
            }
          
        }
            
        
        $total = round($total);





        /////////// block
        $bonus = Salary::selectRaw('sum(ROUND(bonus,0)) as total')
            ->whereMonth('date', $request->month)
            ->whereYear('date', $request['year'])
            ->whereIn('user_id', $user_ids)
            ->first('total')
            ->total;

        $res = $total + $bonus + $kpi_total + $editedBonuses;

        $res = $this->space($res, 3, true);

        return $res;
    }

    public function bonuses(Request $request) {
        $date  = Carbon::parse($request->date);
        return TimetrackingHistory::where('user_id', $request->user_id)
            ->whereYear('date', $date->year)
            ->whereMonth('date', $date->month)
            ->where('description', 'like', 'Добавлен <b>бонус</b>%')
            ->get();
    }
}