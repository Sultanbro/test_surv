<?php

namespace App\Http\Controllers\Salary;

use App\Classes\Helpers\Currency;
use App\Classes\Helpers\Phone;
use App\DayType;
use App\Exports\UsersExport;
use App\Fine;
use App\GroupSalary;
use App\Http\Controllers\Controller;
use App\Kpi;
use App\Models\Admin\EditedBonus;
use App\Models\Admin\EditedKpi;
use App\Models\Admin\EditedSalary;
use App\Models\Admin\ObtainedBonus;
use App\Models\GroupUser;
use App\Models\User\Card;
use App\ProfileGroup;
use App\Salary;
use App\SalaryApproval;
use App\Service\Department\UserService;
use App\Timetracking;
use App\TimetrackingHistory;
use App\User;
use App\UserDescription;
use App\UserFine;
use App\UserNotification;
use Artisan;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;

class SalaryController extends Controller
{

    public function __construct()
    {
        View::share('title', 'Начисления');
        View::share('menu', 'timetrackingaccruals');
        $this->middleware('auth');
    }

    public static function convertCardNumberWithDots($card_number)
    {
        if (empty($card_number) || $card_number == '') return '';
        for ($i = 0; $i <= 12; $i += 4) $card_arr[] = substr($card_number, $i, 4);
        return implode('.', $card_arr);
    }

    // Проверка не уволен ли сотрудник

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

    public function index()
    {
        if (!auth()->user()->can('salaries_view')) {
            return redirect('/');
        }

        $groups = ProfileGroup::where('active', 1);

        if (!auth()->user()->is_admin) $groups->whereIn('id', auth()->user()->groups);


        $groups = $groups->get();

        //if(auth()->id() == 4444) dd($groups);


        $date = Carbon::now()->day(1)->format("Y-m-d");


        if (auth()->user()->is_admin != 1) {
            $_groups = [];
            foreach ($groups as $key => $group) {
                $editors_id = json_decode($group->editors_id);
                if ($editors_id == null) continue;
                if (!in_array(auth()->id(), $editors_id)) continue;

                $approval = SalaryApproval::where('group_id', $group->id)->where('date', $date)->first();
                if ($approval) {
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


        $years = ['2020', '2021', '2022', '2023', '2024']; // TODO Временно. Нужно выяснить из какой таблицы брать динамические годы

        return view('admin.salary', compact('groups', 'years'));
    }

    /**
     * Страница начисления
     * @throws Exception
     */
    public function salaries(Request $request)
    {
        $year = $request->year;
        $month = $request->month;

        $currentUser = auth()->user();

        $date = Carbon::createFromDate($year, $month, 1);

        $data = [];
        $users_ids = [];
        if ($request->has('group_id')) {
            $group = ProfileGroup::find($request->group_id);

            $users = [];

            if ($request->user_types == 0) {
                $users = (new UserService)->getEmployeesForSalaries($request->group_id, $date->format('Y-m-d'));
            }

            if ($request->user_types == 1) {
                $users = (new UserService)->getFiredEmployeesForSalaries($request->group_id, $date->format('Y-m-d'));
            }

            if ($request->user_types == 2) {
                $users = (new UserService)->getTraineesForSalaries($request->group_id, $date->format('Y-m-d'));
            }

            $users_ids = collect($users)->pluck('id')->toArray();

            // if($request->user_types == 2)
        }

        $editors_id = json_decode($group->editors_id);
        $group_editors = is_array($editors_id)
            ? $editors_id
            : [];

        // Доступ к группе
        if (auth()->user()->is_admin != 1 && !in_array($currentUser->id, $group_editors)) {
            return [
                'error' => 'access',
            ];
        }

        //////////////////////

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
            ->where('type', GroupSalary::WORKING)
            ->get()
            ->sum('total');

        // group fired

        $data['group_fired'] = GroupSalary::where('group_id', $request->group_id)
            ->where('date', $sdate)
            ->where('type', GroupSalary::FIRED)
            ->get()
            ->sum('total');

        $data['accruals'] = GroupSalary::getAccruals($sdate);

        if (Auth::user()->is_admin == 1) {
            $data['all_total'] = GroupSalary::where('date', $sdate)
                ->where('type', GroupSalary::WORKING)
                ->whereNotIn('group_id', [34])
                ->get()->sum('total');

            $data['all_total_fired'] = GroupSalary::where('date', $sdate)
                ->where('type', GroupSalary::FIRED)
                ->whereNotIn('group_id', [34])
                ->get()->sum('total');
        } else {
            $data['all_total'] = 0;
            $data['all_total_fired'] = 0;
        }

        //////
        $groups = ProfileGroup::query()->where('active', true)
            ->orWhere(fn($query) => $query->where('active', false)->where(
                fn($query) => $query->whereNotNull('archived_date')->where(fn($query) => $query->whereDate('archived_date', '>=', $date->format('Y-m-d')))))
            ->get();

        $salary_approved = []; // костыль

        $_groups = [];

        $currentGroup = null;

        $data['salary_approved'] = 0;

        foreach ($groups as $key => $group) {

            $editors_id = json_decode($group->editors_id);
            if ($editors_id == null) $editors_id = [];

            if (auth()->user()->is_admin != 1 && !in_array(auth()->id(), $editors_id)) continue;

            $approval = SalaryApproval::where('group_id', $group->id)->where('date', $sdate)->first();

            if ($approval) {
                $xuser = User::withTrashed()->where('id', ($approval->user_id))->first();
                $group->salary_approved_by = $xuser ? $xuser->last_name . ' ' . $xuser->name : $approval->user_id;
                $group->salary_approved_date = Carbon::parse($approval->updated_at)->format('H:i d.m.Y');
                $group->salary_approved = 1;

                if ($group->id == $request->group_id) {
                    $currentGroup = $group;
                }
            } else {
                $group->salary_approved = 0;
                if ($group->id == $request->group_id) {
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
        $realType = null;
        $date = Carbon::createFromDate($year, $month, $day)->format('Y-m-d');

        $salary = Salary::query()
            ->where('user_id', $user_id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->whereDay('date', $day)
            ->first();

        if ($salary) {
            if ($type == 'avans') {
                $salary->comment_paid = $comment;
                $salary->paid = $amount;
                $realType = 'paid';
            }

            if ($type == 'bonus') {
                $salary->comment_bonus = $comment;
                $salary->bonus = $amount;
                $realType = 'bonus';
            }

            $salary->save();
        } else {
            if ($type == 'avans') {
                $salary = Salary::query()->create([
                    'user_id' => $user_id,
                    'date' => $date,
                    'amount' => 0,
                    'comment_paid' => $comment,
                    'paid' => $amount,
                ]);
                $realType = 'paid';

            }

            if ($type == 'bonus') {
                $salary = Salary::query()->create([
                    'user_id' => $user_id,
                    'date' => $date,
                    'amount' => 0,
                    'comment_bonus' => $comment,
                    'bonus' => $amount,
                ]);
                $realType = 'bonus';

            }

        }

        if ($type == 'avans' || $type == 'bonus') {
            if ($type == 'avans') $text = 'аванс';
            if ($type == 'bonus') $text = 'бонус';

            $author = Auth::user()->last_name . ' ' . Auth::user()->name;
            UserNotification::create([
                'user_id' => $user_id,
                'about_id' => $user_id,
                'title' => 'Добавлен ' . $text,
                'group' => now(),
                'message' => $author . ': ' . $comment
            ]);
        }

        if ($type == 'avans') $type = 'аванс';
        if ($type == 'bonus') $type = 'бонус';

        $editor = Auth::user();

        TimetrackingHistory::query()->create([
            'user_id' => $user_id,
            'author_id' => $editor->id,
            'author' => $editor->last_name . ' ' . $editor->name,
            'date' => $date,
            'description' => 'Добавлен <b>' . $type . '</b> на сумму ' . $amount . '<br> Комментарии: ' . $comment,
            'payload' => json_encode([
                'type' => $realType,
                'amount' => $amount,
                'salary_id' => $salary->getKey()
            ])
        ]);

        return json_encode([
            'author' => Auth::user()->last_name . ' ' . Auth::user()->name,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'description' => 'Добавлен <b>' . $type . '</b> на сумму ' . $amount . '<br> Комментарии: ' . $comment,
            'day' => $request->day,
            'date' => $date,
        ]);
    }

    /**
     * @throws Exception
     */
    public function exportExcel(Request $request)
    {
        $rules = [
            'year' => 'required',
            'month' => 'required',
            'group_id' => 'required',
        ];

        $currentUser = auth()->user();

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->to('/timetracking/salaries')->withErrors('Поля не введены');
        }

        /** @var ProfileGroup $group */
        $group = ProfileGroup::query()->find($request->get('group_id'));
        $date = Carbon::createFromDate($request->get('year'), $request->get('month'), 1);

        /**
         * get users
         */
        $working_users = (new UserService)->getEmployeesForSalaries($request->get('group_id'), $date->format('Y-m-d'));
        $working_users = collect($working_users)->pluck('id')->toArray();

        $fired_users = (new UserService)->getFiredEmployeesForSalaries($request->get('group_id'), $date->format('Y-m-d'));
        $fired_users = collect($fired_users)->pluck('id')->toArray();

        $editors_id = json_decode($group->editors_id);
        $group_editors = is_array($editors_id) ? $editors_id : [];

        // Доступ к группе
        if (auth()->user()->is_admin != 1) {
            if (!in_array($currentUser->id, $group_editors)) {
                return [
                    'error' => 'access',
                ];
            }
        }

        $headings = [
            'ФИО', // 0
            'На карте', // 1
            'Возраст', // 2
            'Телефон', // 3
            'Карта', // 4
            'Тип', // 5
            'ИИН', // 6
            'Отр. дни', // 7
            'Раб. дни', // 8
            'Ставка', // 9
            'Начислено', // 10
            'KPI', // 11
            'Стажировочные', // 12
            'Бонус', // 13
            'ИТОГО', // 14
            'Авансы', // 15
            'Штрафы', // 16
            'Налоги' // 17
        ];

        array_push($headings, 'ИТОГО расход', 'К выдаче', 'В валюте');

        $data = [];

        $date = Carbon::createFromDate($request->get('year'), $request->get('month'), 1);

        $working_users = $this->getSheet($working_users, $date, $request->get('group_id'));
        $fired_users = $this->getSheet($fired_users, $date, $request->get('group_id'));

        $_users = array_merge([['']], $working_users['users']);
        $_users = array_merge($_users, [[''], [''], ['']]);
        $_users = array_merge($_users, $fired_users['users']);

        $data[0] = [
            'name' => 'Действующие и Уволенные',
            'sheet' => $_users,
            'headings' => $headings,
            'counter' => count($working_users['users']) - 1
        ];

        if (ob_get_length() > 0) ob_clean(); //  ob_end_clean();
        $edate = $date->format('m.Y');

        $exp = new UsersExport($data[0]['name'], $data[0]['headings'], $data[0]['sheet'], $group, $data[0]['counter'], $date);
        $exp_title = 'Начисления ' . $edate . ' "' . $group->name . '".xlsx';

        return Excel::download($exp, $exp_title);
    }

    /**
     * @throws Exception
     */
    private function getSheet($users_ids, Carbon $date, $group_id)
    {
        $lastDay = $date->copy()->endOfMonth()->toDateString();
        $latestHistories = \DB::table('histories')
            ->selectRaw('MAX(id) as id, reference_id')
            ->where('reference_table', 'App\\User')
            ->where('type', 2)
            ->whereDate('created_at', '<=', $lastDay)
            ->groupBy('reference_id');

        $users = \DB::table('users')
            ->leftJoinSub($latestHistories, 'latest_histories', function ($join) {
                $join->on('users.id', '=', 'latest_histories.reference_id');
            })
            ->leftJoin('histories as h', 'h.id', '=', 'latest_histories.id')
            ->join('zarplata as z', 'z.user_id', '=', 'users.id')
            ->whereIn('users.id', array_unique($users_ids))
            ->selectRaw("users.id as id,
                        users.phone as phone,
                        users.program_id as program_id,
                        CONCAT(users.last_name,' ',users.name) as full_name,
                        users.working_time_id as working_time_id,
                        users.working_day_id as working_day_id,
                        users.birthday as birthday,
                        z.zarplata as salary,
                        z.card_kaspi as card_kaspi,
                        z.kaspi_cardholder as kaspi_cardholder,
                        z.jysan_cardholder as jysan_cardholder,
                        z.card_jysan as card_jysan,
                        z.kaspi as kaspi,
                        z.jysan as jysan,
                        users.currency as currency,
                        CONCAT('KASPI', '') as card,
                        users.uin as uin,
                        users.user_type as user_type,
                        h.payload as history_payload,
                        h.created_at as history_created_at
                        ")
            ->groupBy('id', 'phone', 'full_name', 'working_time_id', 'salary',
                'card_kaspi', 'card_jysan', 'jysan', 'kaspi', 'kaspi_cardholder', 'jysan_cardholder', 'card', 'user_type', 'program_id', 'birthday', 'currency', 'working_day_id', 'uin')
            ->get();

        $fines = Fine::query()->pluck('penalty_amount', 'id')->toArray();
        $data = [];

        $allTotal = [
            0 => '', // name
            1 => '', // card name
            2 => 0, // age
            3 => '', // phone
            4 => '', // card detail
            5 => '', // user type
            6 => 0, // uin
            7 => 0, // Отр. дни
            8 => 0, // working days
            9 => 0, // stavka
            10 => 0, // nachisleniya
            11 => 0, // kpi
            12 => 0, // trainee
            13 => 0, // bonus
            14 => 0, // itog
            15 => 0, // avans
            16 => 0, // fine
            17 => 0, // taxes,
            18 => 0, // ИТОГО расход
            19 => 0, // К выдаче
            20 => 0 // В валюте
        ];

        $data['users'] = [];


        $tax_amount = 0;
        foreach ($users as $user) {
            /** @var User $user */
            /** @var User $_user */
            $_user = User::withTrashed()->find($user->id);

            /** @var UserDescription $ud */
            $ud = UserDescription::query()->where('user_id', $user->id)->first();

            if ($ud && $ud->is_trainee != 0) {
                continue;
            }

            // Суммы на месяц
            /** @var Salary $month_salary */
            $month_salary = Salary::query()
                ->where('user_id', $user->id)
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->select([
                    DB::raw('sum(paid) as avans'),
                    DB::raw('sum(bonus) as bonus'),
                ])
                ->first();

            // edited salary
            /** @var EditedSalary $edited_salary */
            $edited_salary = EditedSalary::query()
                ->where('user_id', $user->id)
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->first();
            $edited_salary_amount = $edited_salary ? $edited_salary->amount : 0;

            // Карты и держатели карт
            $cards = '';
            $cardholder = '';
            if ($user->kaspi) {
                $cardholder = $user->kaspi_cardholder;
                $cards = 'KASPI: ' . $this->phone_space($user->kaspi) . '; ' . $this->card_space($user->card_kaspi);
            } else if ($user->card_kaspi) {
                $cardholder = $user->kaspi_cardholder;
                $cards = 'KASPI: ' . $this->card_space($user->card_kaspi);
            } else if ($user->jysan) {
                $cardholder = $user->jysan_cardholder;
                $cards = 'JYSAN: ' . $this->phone_space($user->jysan) . '; ' . $this->card_space($user->card_jysan);
            } else if ($user->card_jysan) {
                $cardholder = $user->jysan_cardholder;
                $cards = 'JYSAN: ' . $this->card_space($user->card_jysan);
            } else {
                /** @var Card $user_card */
                $user_card = Card::query()->where('user_id', $user->id)->first();
                if ($user_card) {
                    $cardholder = $user_card->cardholder;
                    $_card = ($user_card->number) ? $this->card_space($user_card->number) . '; ' . $this->phone_space($user_card->phone) : $this->phone_space($user_card->phone);
                    $cards = $user_card->bank . '(' . $user_card->country . '): ' . $_card;
                }
            }

            // рабочие дни
            $workChartFromHistory = null;
            if ($user->history_payload && !$date->isCurrentMonth()) {
                $payload = json_decode($user->history_payload, true);
                $workChartFromHistory = $payload['work_chart_id'] ?? null;
            }

            if ($_user) $workDays = $_user->getWorkDays($date, $workChartFromHistory);
            else return throw new Exception("User not found");

            if (auth()->id() == 5 && $user->id == 27565) {
                dd($workDays, $workChartFromHistory, $_user->getWorkChart($workChartFromHistory), $date);
            }

            if (!$edited_salary) $allTotal[8] += $workDays;

            // стажировочные
            $trainee_days = DayType::query()
                ->select([DB::raw('DAY(date) as day')])
                ->where('user_id', $user->id)
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->whereIn('type', [5, 6, 7])
                ->get(['day'])
                ->pluck('day')
                ->toArray();

            // отработанные дни
            $workedHours = Timetracking::query()
                ->select([
                    'total_hours',
                    DB::raw('DAY(enter) as day')
                ])
                ->whereYear('enter', $date->year)
                ->whereMonth('enter', $date->month)
                ->where('user_id', $user->id)
                ->get();

            $workedHours = $workedHours->whereNotIn('day', $trainee_days)->sum('total_hours') / 60;
            $workedDays = round($workedHours / $_user->countWorkHours(), 2);

            if (!$edited_salary) $allTotal[7] += $workedDays;

            // проверка сданных экзаменов
            $wage = $user->salary; // WAGE: оклад + бонус от экзамена

            // ставка
            $allTotal[9] += intval($wage) ?? 0;

            $salary_table = Salary::salariesTable(-1, $date->format('Y-m-d'), [$user->id], $group_id);

            $salary = 0;
            $trainee_fees = 0;
            $totalTaxesUser = 0;

            if (count($salary_table['users']) > 0) {
                $arrs = $salary_table['users'][0];
                $totalTaxesUser = $arrs['totalTaxes'];
                for ($i = 1; $i <= $date->daysInMonth; $i++) {
                    if ($arrs->trainings[$i]) {
                        $trainee_fees += $arrs->earnings[$i] ?? 0;
                    } else {
                        $salary += $arrs->earnings[$i] ?? 0;
                    }
                }
            }

            // KPI
            /** @var EditedKpi $editedKpi */
            $editedKpi = EditedKpi::query()
                ->where('user_id', $user->id)
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->first();

            $kpi = $editedKpi ? $editedKpi->amount : Kpi::userKpi($user->id, $date->format('Y-m-d'));

            if (!$edited_salary) {
                $allTotal[10] += $salary;
                $allTotal[11] += $kpi;
                $allTotal[12] += $trainee_fees;
            }

            // Бонусы
            /** @var EditedBonus $editedBonus */
            $editedBonus = EditedBonus::query()
                ->where('user_id', $user->id)
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->first();

            if ($editedBonus) {
                $bonus = $editedBonus->amount;
            } else {
                $ObtainedBonus = ObtainedBonus::onMonth($user->id, $date->format('Y-m-d'));

                $test_bonuses = 0;
//                    TestBonus::selectRaw('sum(ROUND(amount,0)) as total')
//                    ->where('user_id', $user->id)
//                    ->whereYear('date', $date->year)
//                    ->whereMonth('date', $date->month)
//                    ->first('total')
//                    ->total;

                $bonus = round($month_salary->bonus + $ObtainedBonus + $test_bonuses);
            }

            if (!$edited_salary) $allTotal[13] += $bonus;

            // ИТОГО доход
            $total_income = round($salary + $bonus + $kpi + $trainee_fees);

            if (!$edited_salary) $allTotal[14] += $total_income;

            // headphone price minus
            $headphones_amount = 0;
            if ($ud) {
                $headphones_date = Carbon::parse($ud->headphones_date);
                if ($ud->headphones_amount > 0 &&
                    $headphones_date->year == $date->year &&
                    $headphones_date->month == $date->month) {
                    $headphones_amount = $ud->headphones_amount;
                }
            }

            $prepaid = round($month_salary->avans) + $headphones_amount;
            if (!$edited_salary) $allTotal[15] += $prepaid;

            // Не показывать если все по нулям
            if ($edited_salary && $edited_salary->amount == 0) {
                continue;
            }

            if ($salary == 0 && $bonus == 0 && $prepaid == 0 && $trainee_fees == 0 && $edited_salary_amount == 0) {
                continue;
            }

            // Штрафы
            $penalty = 0;
            $userFines = UserFine::query()
                ->whereYear('day', $date->year)
                ->whereMonth('day', $date->month)
                ->where('user_id', $user->id)
                ->where('status', UserFine::STATUS_ACTIVE)
                ->get();

            foreach ($userFines as $fine) {
                $penalty += $fines[$fine->fine_id];
            }

            if (!$edited_salary) $allTotal[16] += $penalty;


            // Итого расход
            $expense = $prepaid + $penalty;
            if (!$edited_salary) $allTotal[18] += $expense;

            // К выдаче
            $total_payment = round($total_income - $expense - $totalTaxesUser);

            if (!$edited_salary) $allTotal[19] += max($total_payment, 0);

            // В валюте
            $currency_rate = in_array($user->currency, array_keys(Currency::rates())) ? (float)Currency::rates()[$user->currency] : 0.0000001;

            // Колонки для excel.
            $totalColumns = [
                0 => $user->full_name, // Действующие
                1 => $cardholder, // Card Holder name
                2 => $user->birthday ? floor((strtotime(now()) - strtotime($user->birthday)) / 3600 / 24 / 365) : '', // Возраст
                3 => $user->phone ? ' +' . Phone::normalize($user->phone) : '',
                4 => $cards, // Номер карты
                5 => $user->user_type ?? '', // Номер карты
                6 => $user->uin ?? '', // иин
                7 => $workedDays, // отработанные дни
                8 => $workDays, // рабочие дни
                9 => $wage ?? 0, // ставка
                10 => 0, // начислено
                11 => 0, // KPI
                12 => 0, // стажировочные
                13 => 0, //
                14 => 0, // ИТОГО доход,
                15 => 0, // Авансы
                16 => 0, // Штрафы
                17 => $totalTaxesUser, // taxes,
                18 => 0, // ИТОГО расход
                19 => 0, // К выдаче
                20 => 0 // В валюте
            ];

            /**
             * Расчет налогов.
             */
            $allTotal[17] += $totalTaxesUser;

            $on_currency = number_format($total_payment * $currency_rate, 0, '.', '') . strtoupper($user->currency);

            try {
                if ($edited_salary) {
                    $allTotal[10] += (float)$edited_salary->amount;
                    $on_currency = number_format((float)$edited_salary->amount * $currency_rate, 0, '.', '') . strtoupper($user->currency);
                    $totalColumns[10] = 15;
                    $totalColumns[14] = $edited_salary->amount;
                    $totalColumns[20] = $this->space($edited_salary->amount, 3, true);

                } else {
                    $totalColumns[10] = (int)$salary;
                    $totalColumns[11] = $kpi;
                    $totalColumns[12] = $trainee_fees ?? 0;
                    $totalColumns[13] = $bonus;
                    $totalColumns[14] = $total_income;
                    $totalColumns[15] = $prepaid;
                    $totalColumns[16] = $penalty;
                    $totalColumns[18] = $expense;
                    $totalColumns[19] = $this->space($total_payment, 3, true);
                }
                $totalColumns[20] = $this->space($on_currency, 3, true);
                $data['users'][] = $totalColumns;
            } catch (Exception $e) {
                dd($e);
            }
        }
        // сортировка по имени
        $name_asc = array_column($data['users'], 0);
        array_multisort($name_asc, SORT_ASC, $data['users']);

        // К выдаче сумма форматированная
        $allTotal[11] = $this->space(round($allTotal[11]), 3, true);
        $allTotal[18] = $this->space(round($allTotal[18]), 3, true);
        $allTotal[19] = $this->space(round($allTotal[19] - $tax_amount), 3, true);

        // Итоги в конце таблицы
        $data['users'][] = $allTotal;

        return $data;
    }

    private function phone_space($phone)
    {
        if ($phone == null) return '';
        $string = preg_replace('/[^0-9]/', '', $phone);
        $str = substr($string, 0, 2);
        if (in_array($str, ['77', '87'])) $phone = $this->add_space($string, [1, 4, 7, 9, 11]);
        if (in_array($str, ['99', '33', '38'])) $phone = $this->add_space($string, [3]);
        return $phone;
    }

    private function add_space($str, array $spaces): string
    {
        $result = '';
        for ($i = 0; $i < strlen($str); $i++) {
            if (in_array($i, $spaces)) $result .= ' ';
            $result .= $str[$i];
        }
        return $result;
    }

    private function card_space($card): string
    {
        $card = preg_replace('/\s+/', '', $card);
        return $this->add_space($card, [4, 8, 12, 16]);
    }

    private function space($str, $step, $reverse = false): string
    {
        if ($reverse)
            return strrev(chunk_split(strrev($str), $step, ' '));

        return chunk_split($str, $step, ' ');
    }

    public function getTransfers(Request $request): array
    {
        $request->validate([
            'id' => 'required|int'
        ]);
        return GroupUser::with('profile_group:id,name')
            ->where('user_id', $request['id'])
            ->where('status', GroupUser::STATUS_DROP)
            ->get()
            ->toArray();
    }

    /**
     * approve salary was checked for previous month
     */
    public function approveSalary(Request $request): void
    {
        $approval = SalaryApproval::query()
            ->where('group_id', $request->get('group_id'))
            ->whereMonth('date', $request->get('month'))
            ->whereYear('date', $request->get('year'))
            ->first();

        if (!$approval) {
            SalaryApproval::query()->create([
                'group_id' => $request->get('group_id'),
                'date' => Carbon::createFromDate($request->get('year'), $request->get('month'), 1)->format('Y-m-d'),
                'user_id' => Auth::user()->id
            ]);
        }
    }

    /**
     * Edit bonus or kpi
     */
    public function editPremium(Request $request)
    {
        $type = $request->get('type'); // bonus or kpi
        $user_id = $request->get('user_id');
        $amount = $request->get('amount');
        $comment = $request->get('comment');
        $date = $request->get('date');

        if ($type == 'kpi') {
            $edited = EditedKpi::query()
                ->where('user_id', $user_id)
                ->whereYear('date', Carbon::parse($date)->year)
                ->whereMonth('date', Carbon::parse($date)->month)
                ->first();

            if (!$edited) {
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

        if ($type == 'bonus') {
            $edited = EditedBonus::query()
                ->where('user_id', $user_id)
                ->whereYear('date', Carbon::parse($date)->year)
                ->whereMonth('date', Carbon::parse($date)->month)
                ->first();

            if (!$edited) {
                $edited = new EditedBonus();
            }

            $edited->user_id = $user_id;
            $edited->author_id = auth()->id();
            $edited->amount = $amount;
            $edited->comment = $comment;
            $edited->date = $date;
            $edited->save();
        }

        if ($type == 'final') {
            $edited = EditedSalary::where('user_id', $user_id)
                ->whereYear('date', Carbon::parse($date)->year)
                ->whereMonth('date', Carbon::parse($date)->month)
                ->first();

            if (!$edited) {
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
        foreach ($user_ids as $user_id) {
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

        foreach ($tts as $tt) {


            $user = User::withTrashed()->find($tt->user_id);
            if ($user) {
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

    /**
     * @param Request $request
     * @return mixed
     */
    public function bonuses(Request $request): mixed
    {
        $date = Carbon::parse($request->date);
        return TimetrackingHistory::withTrashed()
            ->where('user_id', $request->user_id)
            ->whereYear('date', $date->year)
            ->whereMonth('date', $date->month)
            ->where('description', 'like', 'Добавлен <b>бонус</b>%')
            ->get();
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function advances(Request $request): mixed
    {
        $date = Carbon::parse($request->date);
        return TimetrackingHistory::withTrashed()
            ->where('user_id', $request->user_id)
            ->whereYear('date', $date->year)
            ->whereMonth('date', $date->month)
            ->where('description', 'like', 'Добавлен <b>аванс</b>%')
            ->get();
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function fines(Request $request): Collection
    {
        $date = Carbon::parse($request->get('date'));
        /** @var User $user */
        $user = User::query()->find($request->get('user_id'));
        return $user->fines()
            ->whereYear('day', $date->year)
            ->whereMonth('day', $date->month)
            ->get();
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function taxes(Request $request): mixed
    {
        $date = Carbon::parse($request->date);
        $user = User::withTrashed()->find($request->user_id);

        return $user->taxes()
            ->whereYear('user_tax.created_at', $date->year)
            ->whereMonth('user_tax.created_at', $date->month)
            ->get();
    }

    private function showFiredEmployee($user, $month, $year)
    {
        if ($user->deleted_at == '0000-00-00 00:00:00' || $user->deleted_at == null) { // Проверка не уволен ли сотрудник
            return true;
        } else {

            $dt1 = Carbon::parse($user->deleted_at); // День увольнения
            $dt2 = Carbon::create($year, $month, 30, 0, 0, 0); // Выбранный период

            if ($dt1 >= $dt2) {
                if (count($user->fines) != 0) { // Проверка есть ли хоть одна fine user-a
                    return true;
                }
            } else if ($dt1->month == $dt2->month && $dt1->year == $dt2->year) { // Проверка совпадают ли месяцы
                return true;
            }
        }
    }
}
