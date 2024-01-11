<?php

namespace App\Http\Controllers\Timetrack;

use App\Api\BitrixOld as Bitrix;
use App\Classes\Helpers\Currency;
use App\Classes\Helpers\Phone;
use App\DayType;
use App\Downloads;
use App\Events\WorkdayEvent;
use App\Facade\Referring;
use App\Fine;
use App\Http\Controllers\Controller;
use App\Http\Requests\TimeTrack\AcceptOvertimeRequest;
use App\Http\Requests\TimeTrack\OvertimeRequest;
use App\Http\Requests\TimeTrack\RejectOvertimeRequest;
use App\Http\Requests\TimeTrack\StartOrStopTrackingRequest;
use App\Models\Admin\EditedBonus;
use App\Models\Admin\EditedKpi;
use App\Models\Admin\ObtainedBonus;
use App\Models\Analytics\Activity;
use App\Models\Bitrix\Lead;
use App\Models\Books\BookGroup;
use App\Models\GroupUser;
use App\Models\Kpi\Bonus;
use App\Models\TestBonus;
use App\Models\Timetrack\UserPresence;
use App\Models\User\NotificationTemplate;
use App\Position;
use App\PositionDescription;
use App\ProfileGroup;
use App\ProfileGroupUser as PGU;
use App\Salary;
use App\Service\Bonus\ObtainedBonusService;
use App\Service\Bonus\TestBonusService;
use App\Service\Department\UserService;
use App\Service\Fine\FineService;
use App\Service\GroupUserService;
use App\Service\Salary\SalaryService;
use App\Service\Timetrack\AcceptOvertimeService;
use App\Service\Timetrack\OvertimeService;
use App\Service\Timetrack\RejectOvertimeService;
use App\Service\Timetrack\TimetrackService;
use App\Setting;
use App\Timetracking;
use App\TimetrackingHistory;
use App\User;
use App\UserAbsenceCause;
use App\UserDeletePlan;
use App\UserDescription;
use App\UserFine;
use App\UserNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class TimetrackingController extends Controller
{
    public function __construct(
        public SalaryService        $salaryService,
        public FineService          $fineService,
        public ObtainedBonusService $obtainedBonusesService,
        public TestBonusService     $testBonusService,
    )
    {
        $this->middleware('auth');
    }

    public function settings()
    {
        View::share('title', 'Настройки');
        View::share('menu', 'timetrackingsetting');

        $groups = ProfileGroup::where('active', 1)->get(['id', 'name'])->pluck('name', 'id');


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

        if (isset($_GET['tab'])) {
            $active_tab = (int)$_GET['tab'];

            if ($active_tab == 1 && !auth()->user()->can('users_view')) return redirect('/');
            if ($active_tab == 2 && !auth()->user()->can('positions_view')) return redirect('/');
            if ($active_tab == 3 && !auth()->user()->can('groups_view')) return redirect('/');
            if ($active_tab == 4 && !auth()->user()->can('fines_view')) return redirect('/');
            if ($active_tab == 5 && !auth()->user()->can('notifications_view')) return redirect('/');
            if ($active_tab == 6 && !auth()->user()->can('permissions_view')) return redirect('/');
            if ($active_tab == 7 && !auth()->user()->can('checklists_view')) return redirect('/');
        } else {
            if (!(auth()->user()->can('settings_view') ||
                auth()->user()->can('users_view') ||
                auth()->user()->can('positions_view') ||
                auth()->user()->can('groups_view') ||
                auth()->user()->can('fines_view') ||
                auth()->user()->can('notifications_view') ||
                auth()->user()->can('permissions_view') ||
                auth()->user()->can('checklists_view')
            )) {
                return redirect('/');
            }

            if (auth()->user()->can('settings_view') || auth()->user()->can('users_view')) {
                $active_tab = 1;
            } else if (auth()->user()->can('positions_view')) {
                $active_tab = 2;
            } else if (auth()->user()->can('groups_view')) {
                $active_tab = 3;
            } else if (auth()->user()->can('fines_view')) {
                $active_tab = 4;
            } else if (auth()->user()->can('notifications_view')) {
                $active_tab = 5;
            } else if (auth()->user()->can('permissions_view')) {
                $active_tab = 6;
            } else if (auth()->user()->can('checklists_view')) {
                $active_tab = 7;
            }
        }


        $corpbooks = [];
        if ($active_tab == 3) {
            $corpbooks = \App\KnowBase::where('parent_id', null)->get();
        }

        $tab5 = [
            'users' => [],
            'templates' => [],
            'positions' => [],
        ];

        if ($active_tab == 5 || $active_tab == 1) {

            $users = User::withTrashed()->select(DB::raw("CONCAT_WS(' ',ID, last_name, name) as name"), 'ID as id')->get()->toArray();
            $tab5['users'] = array_values($users);

            $positions = Position::select('position as name', 'id')->get()->toArray();

            $tab5['positions'] = array_values($positions);

        }

        $groupsWithId = ProfileGroup::select('name', 'id')->get();
        return view(
            'admin.settingtimetracking')
            ->with('positions', $positions)
            ->with('groups', $groups)
            ->with('archived_groups', $archived_groups)
            ->with('book_groups', $book_groups)
            ->with('corpbooks', $corpbooks)
            ->with('active_tab', $active_tab)
            ->with('tab5', $tab5)
            ->with('groupsWithId', $groupsWithId);
    }

    public function fines()
    {
        View::share('menu', 'fines');
        $fines = $this->fineService->getFines();
        return view('admin.fines', compact('fines'));
    }

    public function info()
    {
        View::share('menu', 'info');
        View::share('title', 'Частые вопросы');
        return view('admin.info');
    }

    public function addPosition(Request $request)
    {
        $pos = Position::where('position', $request->position)->first();
        if ($pos) {
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

    public function deletePosition(Request $request)
    {
        $pos = Position::where('position', $request->position)->first();

        if ($pos) {
            $pos->delete();
        }
    }

    public function getPosition(Request $request)
    {
        $pos = Position::where('id', $request->name)->first();

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

        if ($request->desc) {
            $pd = PositionDescription::where('position_id', $request->id)->first();
            if ($pd) $pd->delete();
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

    /**
     * Handle startDay btn clicks
     * @throws Exception
     */
    public function timetracking(StartOrStopTrackingRequest $request): JsonResponse
    {
        $userClickedStart = $request->has('start');

        $user = Auth::user();
        $check_workdays = $user->checkWorkdaysForStartTracking();

        if (!$check_workdays) {              // если сегодня не рабочий день, то не реагирует на нажатие и выйдет уведомления о том что сегодня выходной
            return response()->json([
                'status' => false
            ]);
        }

        $status = $userClickedStart
            ? $this->startDay()
            : $this->endDay();

        return response()->json([

            // status Started or Stopped day
            'status' => $status,

            // Cтраница из Базы знаний
            // Показывается при начале дня Сотрудника
            // Сотрудник обязан читать минимум 60 сек
            'corp_book' => auth()->user()->getCorpbook()
        ]);

    }

    private function endDay(): string
    {
        $user = auth()->user();
        $schedule = $user->schedule();
        $now = Carbon::now($user->timezone());

        $workday = $user->timetracking()->whereDate('enter', $now->format('Y-m-d'))->first();

        if (!$workday) {
            throw new \Exception('Вы еще не начинали рабочий день!');
        }

        if ($workday && $workday->isEnded()) {
            throw new \Exception('Вы уже завершили рабочий день!');
        }

        $exit = $schedule['end']->isPast() ? $schedule['end'] : $now;

        $workday->setExit($exit)
            ->setStatus(Timetracking::DAY_ENDED)
            ->addTime($exit, $user->timezone())
            ->save();

        Referring::touchReferrerSalaryWeekly($user, $exit);

        return 'stopped';
    }

    /**
     * @throws Exception
     */
    private function startDay($userId = null): string
    {
        $user = $userId ? User::find($userId) : auth()->user();
        $schedule = $user->schedule();

        $now = Carbon::now($user->timezone());

        $workday = $user->timetracking()->whereDate('enter', $now->format('Y-m-d'))->first();

        //Нажал "Начать день"
        if ($workday && $workday->isStarted()) {
            throw new Exception('Вы уже начали рабочий день!');
        }

        if ($schedule['start']->isFuture()) {
            throw new Exception('Вы не можете начать день до ' . $schedule['start']->format('H:i'));
        }

        if ($schedule['end']->isPast()) {
            throw new Exception('Вы не можете работать после ' . $schedule['end']->format('H:i'));
        }

        WorkdayEvent::dispatch($user);

        if ($workday) {
            $workday->setEnter($now)
                ->setStatus(Timetracking::DAY_STARTED)
                ->addTime($now, $user->timezone())
                ->save();
        }

        if (!$workday) {
            Timetracking::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'enter' => $now->setTimezone('UTC')
                ],
                [
                    'times' => [$now->setTimezone('UTC')->format('H:i')],
                    'status' => Timetracking::DAY_STARTED
                ]
            );
        }

        return 'started';
    }

    /**
     * Наверное для того чтобы узнать начинал рабочий день или нет
     * Еще вычисляет баланс к выдаче
     *
     * @TODO Decompose this method
     *
     * @return JsonResponse
     */
    public function trackerstatus(Request $request)
    {
        $user = auth()->user();

        return response()->json([
            'status' => $user->timetracking()->running()->first() ? 'started' : 'stopped',
            'balance' => [
                'currency' => strtoupper($user->currency),
            ],
            'corp_book' => $user->getCorpbook()
        ]);
    }

    public function savegroup(Request $request)
    {
        $pg = ProfileGroup::where('name', $request->group)->first();

        if ($pg) {
            return response()->json([
                'status' => 0,
                'group' => $pg,
            ]);
        } else {
            $added = ProfileGroup::create([
                'name' => $request->group,
                'editors_id' => '[]',
                'users' => '[]',
            ]);
            return response()->json([
                'status' => 1,
                'group' => $added,
            ]);
        }


    }

    public function deletegroup(Request $request)
    {
        $group = ProfileGroup::where('id', $request->group)->first();
        $group->active = 0;
        $group->archived_date = Carbon::now()->toDateString();
        $group->save();
        return 'true';
    }

    public function getusersgroup(Request $request)
    {
        $corp_books = [];

        if ($request->group) {

            $group = ProfileGroup::find($request->group);

            $users = (new UserService)->getUsers($group->id, date('Y-m-d'));

            $users = collect($users)
                ->map(function ($item) {
                    $item->email = $item->last_name . ' ' . $item->name . ' ' . $item->email;
                    return $item;
                });


            $kbm = \App\Models\KnowBaseModel::
            where('model_type', 'App\\ProfileGroup')
                ->where('model_id', $group->id)
                ->where('access', 1)
                ->get()
                ->pluck('book_id')
                ->toArray();

            $corp_books = \App\KnowBase::whereIn('id', array_unique($kbm))->get();

            $bonus = Bonus::where('group_id', $group->id)->first();


        } else {
            $users = User::get(['id', DB::raw("CONCAT(name,' ',last_name,'-',email) as email")]);

            foreach ($users as $user) {
                if ($user->email == '') $user->email = 'x';
            }
        }


        $bonuses = isset($group) ? Bonus::where('group_id', $group->id)->get() : [];

        $activities = isset($group) ? Activity::where('group_id', $group->id)->get(['name', 'id'])->toArray() : [];

        $payment_terms = isset($group) ? $group->payment_terms : '';

        //time_exceptions

        return response()->json([
            'name' => isset($group) ? $group->name : 'Noname',
            'users' => isset($users) ? $users : [],
            'corp_books' => $corp_books,
            'group_id' => isset($group) ? $group->id : 0,
            'timeon' => isset($group->work_start) ? $group->work_start : "00:00",
            'timeoff' => isset($group->work_end) ? $group->work_end : "00:00",
            'plan' => isset($group->plan) ? $group->plan : 0,
            'zoom_link' => isset($group->zoom_link) ? $group->zoom_link : '',
            'bp_link' => isset($group->bp_link) ? $group->bp_link : '',
            'dialer_id' => isset($group->dialer) ? $group->dialer->dialer_id : null,
            'script_id' => isset($group->dialer) ? $group->dialer->script_id : null,
            'talk_minutes' => isset($group->dialer) ? $group->dialer->talk_minutes : null,
            'talk_hours' => isset($group->dialer) ? $group->dialer->talk_hours : null,
            'quality' => isset($group) ? $group->quality : 'local',
            'bonuses' => $bonuses,
            'activities' => $activities,
            'payment_terms' => $payment_terms,
            'time_exceptions' => isset($group) ? $group->time_exceptions : [],
            'time_address' => isset($group) ? $group->time_address : 0,
            'workdays' => isset($group) ? $group->workdays : 6,
            'editable_time' => isset($group) ? $group->editable_time : 0,
            'paid_internship' => isset($group) ? $group->paid_internship : 0,
            'show_payment_terms' => isset($group) ? $group->show_payment_terms : 0,
            'groups' => ProfileGroup::where('active', 1)->get()->pluck('name', 'id'),
            'archived_groups' => ProfileGroup::where('active', 0)->get(['name', 'id']),

        ]);
    }

    public function saveusersgroup(Request $request)
    {
        //$group = ProfileGroup::where('name', 'like', '%' . $request->group . '%')->with('dialer')->first();
        $group = ProfileGroup::with('dialer')->find($request->group);
        //
        $users_id = [];
        $groups = ProfileGroup::where('active', 1)->get();
        foreach ($request['users'] as $user) {
            $users_id[] = $user['id'];
        }

        $kbm = \App\Models\KnowBaseModel::
        where('model_type', 'App\\ProfileGroup')
            ->where('model_id', $group->id)
            ->where('access', 1)
            ->delete();

        $corp_books = [];
        foreach ($request['corp_books'] as $corp_book) {
            \App\Models\KnowBaseModel::create([
                'book_id' => $corp_book['id'],
                'model_id' => $group->id,
                'model_type' => 'App\\ProfileGroup',
                'access' => 1,
            ]);
        }

        DB::transaction(function () use (
            $group,
            $request,
            $users_id
        ) {
            $this->insertDataToGroupUser($group, $users_id);

            $group->work_start = $request['timeon'];
            $group->work_end = $request['timeoff'];

            $group->name = $request['gname'];
            $group->zoom_link = $request['zoom_link'];
            $group->bp_link = $request['bp_link'];
            $group->workdays = $request['workdays'];
            $group->payment_terms = $request['payment_terms'];
            $group->editable_time = $request['editable_time'];
            $group->paid_internship = $request['paid_internship'];
            $group->quality = $request['quality'];
            $group->show_payment_terms = $request['show_payment_terms'];

            $group->save();
        });


        if ($request['dialer_id']) {
            if ($group->dialer) {
                $group->dialer->dialer_id = $request['dialer_id'];
                $group->dialer->script_id = $request['script_id'] ?? 0;
                $group->dialer->talk_hours = $request['talk_hours'] ?? 0;
                $group->dialer->talk_minutes = $request['talk_minutes'] ?? 0;
                $group->dialer->save();
            } else {
                \App\Models\CallibroDialer::create([
                    'group_id' => $group->id,
                    'dialer_id' => $request['dialer_id'],
                    'script_id' => $request['script_id'] ?? 0,
                    'talk_hours' => $request['talk_hours'] ?? 0,
                    'talk_minutes' => $request['talk_minutes'] ?? 0
                ]);
            }
        }


        // save users migrations

        $pgu = PGU::where('group_id', $group->id)
            ->where('date', Carbon::now()->day(1)->format('Y-m-d'))
            ->first();

        if ($pgu) {

            $fired = $pgu->fired;
            $fired = array_diff($fired, array_unique($users_id));
            $fired = array_values($fired);
            $pgu->fired = $fired;

            $pgu->assigned = array_values(array_unique($users_id));

            $pgu->save();
        }

        return [
            'groups' => ProfileGroup::where('active', 1)->pluck('name', 'id')->toArray(),
            'group' => $group->id
        ];
    }

    public function applyPerson(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = User::query()->find($request->get('user_id'));
        /** @var User $authUser */
        $authUser = auth()->user();
        $date = now();
        if (!$user) return response()->json(['message' => 'user not found']);

        $user->user_description()->updateOrCreate([
            'user_id' => $user->getKey()],
            [
                'is_trainee' => 0,
                'applied' => $date->format("Y-m-d"),
            ]);

        ///////////////////////////////////////////
        $editPersonLink = 'https://' . tenant('id') . '.jobtron.org/timetracking/edit-person?id=' . $request->get("user_id");

        $timestamp = $date;

        $msg = '<a href="' . $editPersonLink . '" target="_blank">' . $user->last_name . ' ' . $user->name . ' </a><br> ';
        $msg .= 'Рабочий график: ' . $request->get("schedule");

        $notification_receivers = NotificationTemplate::getReceivers(7);

        foreach ($notification_receivers as $user_id) {
            UserNotification::query()->create([
                'user_id' => $user_id,
                'about_id' => $request->user_id,
                'title' => 'Подготовьте документы на принятие на работу',
                'group' => $timestamp,
                'message' => $msg
            ]);
        }

        //////////////////////// Set old notification read
        UserNotification::query()
            ->where('about_id', $request->user_id)
            ->where(function ($query) {
                $query->where('title', 'Пропал с обучения')
                    ->orWhere('title', 'Пропал с обучения: 1 день')
                    ->orWhere('title', 'Пропал с обучения: 2 день');
            })
            ->whereNull('read_at')
            ->update([
                'read_at' => now()
            ]);


        /////////////////////////////////////
        TimetrackingHistory::query()
            ->create([
                'author_id' => auth()->id(),
                'author' => $authUser->name . ' ' . $authUser->last_name,
                'user_id' => $request->get("user_id"),
                'description' => 'Заявка на принятие на работу стажера',
                'date' => date('Y-m-d')
            ]);


        // убрать отметку стажера в этот день

        $daytypes = DayType::query()
            ->where('user_id', $request->get("user_id"))
            ->whereDate('date', now()->format("Y-m-d"))
            ->get();

        foreach ($daytypes as $dt) {
            $dt->update([
                'type' => DayType::DAY_TYPES['APPLIED']
            ]);
        }

        // group provided increment

        /** @var ProfileGroup $group */
        $group = ProfileGroup::query()
            ->find($request->get("group_id"));

        if ($group) {
            $group->provided = $group->provided + 1;
            $group->save();
        }

        // Приглашение в беатрис
        /** @var Lead $lead */
        $lead = Lead::query()
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->first();

        if ($lead && $lead->deal_id != 0) {
            $bitrix = new Bitrix();

            $bitrix->changeDeal($lead->deal_id, [
                'STAGE_ID' => 'C4:WON' // не присутствовал на обучении
            ]);
        }

        Referring::touchReferrerStatus($user);
        Referring::touchReferrerSalaryForCertificate($user);
        Referring::deleteReferrerDailySalary($user->getKey(), $date);


        return response()->json([
            'msg' => 'Заявка отправлена рекрутерам'
        ]);
    }

    public function reports(Request $request)
    {
        if (!auth()->user()->can('tabel_view')) {
            return redirect('/');
        }

        View::share('title', 'Табель сотрудников');
        View::share('menu', 'timetrackingreports');
        $groups = ProfileGroup::where('active', 1)->get();

        if (auth()->user()->is_admin != 1) {
            $_groups = [];
            foreach ($groups as $key => $group) {
                $editors_id = $group->editors_id ? json_decode($group->editors_id) : [];
                if ($editors_id == null) continue;
                if (!in_array(auth()->id(), $editors_id)) continue;
                $_groups[] = $group;
            }
            $groups = $_groups;
        }

        $fines = Fine::selectRaw('id as value, CONCAT(name," - ", penalty_amount) as text')->get();
        $years = ['2020', '2021', '2022']; // TODO Временно. Нужно выяснить из какой таблицы брать динамические годы
        return view('admin.reports', compact('groups', 'fines', 'years'));
    }

    public function getReports(Request $request)
    {

        $currentUser = auth()->user();
        $date = Carbon::createFromDate($request->year, $request->month, 1);
        $group = ProfileGroup::find($request->group_id);

        // Доступ к группе
        $group_editors = is_array(json_decode($group->editors_id)) ? json_decode($group->editors_id) : [];
        if (!in_array($currentUser->id, $group_editors) && $currentUser->is_admin != 1) {
            return [
                'error' => 'access',
            ];
        }

        /**
         * Выбираем кого покзаывать
         */
        if ($request->user_types == 0) { // Действующие
            $users = (new UserService)->getEmployeesForSalaries($request->group_id, $date->format('Y-m-d'));
        }

        if ($request->user_types == 1) { // Уволенныне
            $users = (new UserService)->getFiredEmployeesForSalaries($request->group_id, $date->format('Y-m-d'));
        }

        if ($request->user_types == 2) { // Стажеры
            $users = (new UserService)->getTraineesForSalaries($request->group_id, $date->format('Y-m-d'));
        }

        $_user_ids = collect($users)->pluck('id')->toArray();

        $users = Timetracking::getTimeTrackingReportPaginate($request, $_user_ids, $request->year);

        // ээээм
        $data = [];

        $data['pagination'] = [
            'total' => $users->total(),
            'perPage' => $users->perPage(),
            'lastPage' => $users->lastPage(),
            'currentPage' => $users->currentPage(),
            'nextPageUrl' => $users->nextPageUrl(),
            'previousPageUrl' => $users->previousPageUrl()
        ];

        $data['head_ids'] = [];
        $data['total_resources'] = 0;

        $data['users'] = [];


        foreach ($users as $user) {
            $this->addHours($user);

            $fines = [];
            $daytypes = [];
            $weekdays = [];

            $data['total_resources'] += $user->full_time == 1 ? 1 : 0.5;

            $enable_comment = [ // для стажера
                '1' => 0,
                '2' => 0,
            ];

            for ($i = 1; $i <= $date->daysInMonth; $i++) {
                $fines[$i] = $user->fines->filter(
                    fn($f) => $date->setUnitNoOverflow('day', $i, 'month')->isSameDay($f->pivot->day) && $f->pivot->status == UserFine::STATUS_ACTIVE
                )->pluck('fine_id') ?? [];

                $x = $user->daytypes->where('day', $i)->first();
                $daytypes[$i] = $x->type ?? null;

                $weekdays[$i] = $user->weekdays[(int)$date->day($i)->dayOfWeek] == 1 ? 1 : 0;

                if ($x && in_array($x->type, [2, 5, 7]) && $enable_comment['1'] == 0) {
                    $enable_comment['1'] = $x->day;
                }
                if ($x && in_array($x->type, [2, 5, 7]) && $enable_comment['1'] != $i && $enable_comment['2'] == 0) {
                    $enable_comment['2'] = $x->day;
                }
            }

            $user->enable_comment = $enable_comment;

            $user->selectedFines = $fines;
            $user->dayTypes = $daytypes;
            $user->weekdays = $weekdays;

            $user_applied_at = UserDescription::where('user_id', $user->id)->first();
            if ($user_applied_at && $user_applied_at->applied) {
                $user_applied_at = $user_applied_at->applied;
            } else {
                $user_applied_at = $user->created_at;
            }

            $user->applied_at = 0;
            if (
                $user_applied_at
                && Carbon::parse($user_applied_at)->month == $request->month
                && Carbon::parse($user_applied_at)->year == $request->year
            ) {
                $user->applied_at = Carbon::parse($user_applied_at)->day;
            }

            $data['users'][] = $user;

            foreach ($user->timetracking as $tt) {
                $tt->minutes = $tt->total_hours;
            }

            $trainee = UserDescription::where('is_trainee', 1)->where('user_id', $user->id)->first();

            if ($trainee) {
                $user->is_trainee = true;
                $user->requested = $trainee->applied
                    ? date('d.m.Y H:i', Carbon::parse($trainee->applied)->timestamp + 3600 * 6)
                    : null; // $requested
            } else {
                $user->is_trainee = false;
                $user->requested = null; // $requested
            }

        }

        $data['sum'] = [];
        $data['editable_time'] = $group->editable_time;

        return $data;
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

        $userId = $request->user_id;
        $date = Carbon::createFromDate($request->year, $request->month, $request->day);

        $days = Timetracking::where('user_id', intval($userId))
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
        if ($request->minutes > 300) { // Если человек работал больше 5 часов
            $minutes += 60;  // то добавляется 1 час к обеду
        }
        // Добавить новый exit
        $exit = Carbon::parse($timeStart)->addMinutes(intval($minutes));
        //Конец блока

        if (count($days) > 1) {
            $items = $days->except($days->first()->id)->pluck('id');
            Timetracking::whereIn('id', $items)->delete();
        }

        $employee = User::withTrashed()->find($userId);
        if (count($days) > 0) {
            if ($day->exit == null) {
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

        if ($history) {
            $group = GroupUser::getGroupId($userId);
            if ((new UserService)->getTrainees($group->group_id, $date)) {
                $dayType = DayType::getDayTypeWithDay($userId, $date->format("Y-m-d"));
                if ($dayType) {
                    $dayType->type = DayType::DAY_TYPES['TRAINEE'];
                    $dayType->save();
                }
            }
        }

        $result = [
            'success' => true,
            'history' => $history ?? null
        ];
        return response()->json($result);
    }

    // Проверка не начинал ли сотрудник работу ранее рабочего времени
    public static function checkStartOfDay($request, $day)
    {

        $userProfile = DB::table('users')
            ->select('*')
            ->where('id', '=', $day->user_id)
            ->first();

        $workStart = $userProfile->work_start;

        $workStartInSeconds = strtotime($request->year . '-' . $request->month . '-' . $request->day . ' ' . $workStart);

        $timeStart = $day->enter;

        if ($workStartInSeconds > strtotime($day->enter)) {
            $timeStart = $request->year . '-' . $request->month . '-' . $request->day . ' ' . $workStart;
        }

        return $timeStart;
    }

    public function getNotificationTemplates(Request $request)
    {

        $_users = NotificationTemplate::where('type', NotificationTemplate::USER)->get();

        foreach ($_users as $record) {
            $users = json_decode($record->ids, true);
            $selected = User::select(DB::raw("CONCAT_WS(' ',ID, last_name, name) as name"), 'id')->whereIn("id", $users)->get();
            $record->selectedGroups = $selected->toArray();
        }

        $_groups = NotificationTemplate::where('type', NotificationTemplate::GROUP)->get();

        foreach ($_groups as $record) {
            $groups = json_decode($record->ids, true);
            $selectedGroups = ProfileGroup::select('name', 'id')->whereIn("id", $groups)->get();
            $record->selectedGroups = $selectedGroups->toArray();
        }

        $_positions = NotificationTemplate::where('type', NotificationTemplate::POSITION)->get();

        foreach ($_positions as $record) {
            $positions = json_decode($record->ids, true);
            $selectedGroups = Position::select('position as name', 'id')->whereIn("id", $positions)->get();
            $record->selectedGroups = $selectedGroups->toArray();
        }

        $_others = NotificationTemplate::where('type', NotificationTemplate::OTHER)->get();

        $_notifications = \DB::table('users')
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where('ud.notifications', '!=', '[]')
            ->select(DB::raw("CONCAT_WS(' ',users.id, users.last_name, users.name) as name"), 'users.id as id')
            ->get()->toArray();

        //$_notification_templates = NotificationTemplate::where('type', NotificationTemplate::USER)->select('id', 'title as name')->get()->toArray();
        $_notification_templates = NotificationTemplate::where('type', NotificationTemplate::USER)->select('title', 'id')->get()->toArray();

        $need_group = NotificationTemplate::where('type', NotificationTemplate::USER)->pluck('need_group', 'id')->toArray();


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

    public function updateNotificationTemplate(Request $request)
    {

        $ids = [];
        foreach ($request->ids as $item) {
            array_push($ids, $item['id']);
        }

        $template = NotificationTemplate::find($request->id);

        if ($template->type == 0) { // individual
            $old_ids = json_decode($template->ids);
            $old_ids = array_diff($old_ids, $ids);
            $old_ids = array_values($old_ids);

            // Удалить старых получателей
            foreach ($old_ids as $id) {
                $ud = UserDescription::where('user_id', $id)->first();

                $notis = [];

                foreach ($ud->notifications as $noti) {
                    if ($noti[0] != $request->id) {
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

            foreach ($new_ids as $id) {
                $user_was = false;

                $ud = UserDescription::where('user_id', $id)->first();

                $ns = $ud->notifications;
                foreach ($ns as $noti) {
                    if ($noti[0] == $request->id) {
                        $user_was = true;
                    }
                }

                if (!$user_was) {
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

    public function getgroups(Request $request)
    {
        $user = auth()->user();
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


        $group = ProfileGroup::find($request['group_id']);

        $editors_id = [];

        foreach ($request->users as $user) {
            $editors_id[] = $user['id'];
        }

        if ($group->editors_id == null) $group->editors_id = [];
        $old_editors = json_decode($group->editors_id);
        $group->editors_id = json_encode(array_unique($editors_id));
        $group->save();

        $all_editors = [];
        $groups = ProfileGroup::where('active', 1)->get();
        foreach ($groups as $g) {
            foreach (json_decode($g->editors_id) as $user_id) {
                array_push($all_editors, $user_id);
            }
        }
        $all_editors = array_unique($all_editors);

        return $request->users;
    }

    public function enterreportManually(Request $request)
    {

        if ($request->time == '') {
            return 'The time is not set';
        }

        $timetracking = Timetracking::where('user_id', intval($request->user_id))
            ->whereYear('enter', intval($request->year))
            ->whereMonth('enter', intval($request->month))
            ->whereDay('enter', $request->day)
            ->first();

        $date = $request->day . '.' . $request->month . '.' . $request->year;
        $enter = Carbon::parse($date);
        $enter->setTimeFromTimeString($request->time);

        if ($timetracking) {
            $description = 'Изменено: ' . $request->time . '. ' . $request->comment;
            $timetracking->enter = $enter;
            $timetracking->user_id = $request->user_id;
            $timetracking->updated = 1;
            $timetracking->save();
        } else {
            $description = 'Добавлено: ' . $request->time . '. ' . $request->comment;
            Timetracking::create([
                'enter' => $enter,
                'user_id' => $request->user_id,
                'updated' => 1
            ]);
        }

        TimetrackingHistory::create([
            'author_id' => auth()->user()->id,
            'author' => auth()->user()->name . ' ' . auth()->user()->last_name,
            'user_id' => $request->user_id,
            'description' => $description,
            'date' => $enter
        ]);


    }

    public function enterreport(Request $request)
    {
        if (!auth()->user()->can('entertime_view')) {
            return redirect('/');
        }

        View::share('title', 'Время прихода');
        View::share('menu', 'timetrackingenters');

        $groups = ProfileGroup::where('active', 1)->get();

        if (auth()->user()->is_admin != 1) {

            $_groups = [];
            foreach ($groups as $key => $group) {
                if (!in_array(auth()->id(), json_decode($group->editors_id))) continue;
                $_groups[] = $group;
            }
            $groups = $_groups;
        }

        $currentUser = auth()->user();

        if ($request->isMethod('post')) {

            $group = ProfileGroup::find($request->group_id);

            $date = Carbon::createFromDate($request->year, $request->month, $request->day);

            $workingUsers = (new UserService)->getEmployees($request->group_id, $date->format('Y-m-d'));
            $user_ids = collect($workingUsers)->pluck('id')->toArray();

            // Доступ к группе
            $group_editors = is_array(json_decode($group->editors_id))
                ? json_decode($group->editors_id)
                : [];

            if (!in_array($currentUser->id, $group_editors) && !$currentUser->is_admin) {
                return [
                    'error' => 'access',
                ];
            }

            if (isset($request['filter']) && $request->filter == "deactivated") {
                $users = User::withTrashed()->selectRaw("*,CONCAT(name,' ',last_name) as full_name")
                    ->whereNotNull('deleted_at')
                    ->with([
                        'timetracking' => function ($q) use ($request) {
                            $q->selectRaw("*, DATE_FORMAT(`enter`, '%e') as date")
                                ->orderBy('date')
                                ->whereMonth('enter', $request->month)
                                ->whereYear('enter', $request->year);
                        }
                    ])
                    ->whereIn('id', $user_ids)
                    ->get();
            } elseif (isset($request['filter']) && $request->filter == "trainees") {
                $workingUsers = (new UserService)->getUsers($request->group_id, $date->format('Y-m-d'));
                $user_ids = collect($workingUsers)->pluck('id')->toArray();
                $users = User::withTrashed()->selectRaw("*,CONCAT(name,' ',last_name) as full_name")
                    ->with([
                        'timetracking' => function ($q) use ($request) {
                            $q->selectRaw("*, DATE_FORMAT(`enter`, '%e') as date")
                                ->orderBy('date')
                                ->whereMonth('enter', $request->month)
                                ->whereYear('enter', $request->year);
                        },
                        'description'
                    ])
                    ->whereHas('description', function ($query) {
                        $query->where('is_trainee', 1)
                            ->where('fire_date', null);
                    })
                    ->whereIn('id', $user_ids)
                    ->get();
            } else {

                $users = User::withTrashed()->selectRaw("*,CONCAT(name,' ',last_name) as full_name")
                    ->with([
                        'timetracking' => function ($q) use ($request) {
                            $q->selectRaw("*, DATE_FORMAT(`enter`, '%e') as date")
                                ->orderBy('date')
                                ->whereMonth('enter', $request->month)
                                ->whereYear('enter', $request->year);
                        }
                    ])
                    ->whereNull('deleted_at')
                    ->whereIn('id', $user_ids)
                    ->get();
            }

            $data = [];
            foreach ($users as $userData) {

                $userfines = UserFine::query()
                    ->where('user_id', $userData->id)
                    ->whereMonth('day', $request->month)
                    ->whereYear('day', $request->year)
                    ->where('status', 1)
                    ->whereIn('fine_id', [1, 2])
                    ->get();

                foreach ($userfines as $fine) {
                    $fine->day = substr($fine->day, 8, 2);
                }

                $days = array_unique($userData->timetracking->pluck('date')->toArray());


                foreach ($days as $day) {
                    $data[$userData->id][$day] = $userData->timetracking
                        ->where('date', $day)
                        ->min('enter')
                        ->setTimezone(Setting::TIMEZONES[6])
                        ->format('H:i');
                }

                $fines = [];
                for ($i = 1; $i <= $date->daysInMonth; $i++) {
                    $d = $i;
                    if (strlen($i) == 1) $d = '0' . $i;

                    $x = $userfines->where('day', $d);
                    if ($x->count() > 0) {
                        $fines[$i] = ['yes'];
                    } else {
                        $fines[$i] = [];
                    }
                }

                $data[$userData->id]['fines'] = $fines;
                $data[$userData->id]['name'] = $userData->full_name;
                $data[$userData->id]['user_id'] = $userData->id;
            }

            return array_values($data);
        }

        $years = ['2020', '2021', '2022']; // TODO Временно. Нужно выяснить из какой таблицы брать динамические годы
        return view('admin.enter-report', compact('groups', 'years'));
    }

    public function zarplatatable(Request $request)
    {
        $user = User::find(5);
        // me($user->workingDay);
        // me($user->zarplata);

        /**
         * Prepare zarplata vars and count hourly pay
         */

        try {
            $currency_rate = (float)Currency::rates()[$user->currency];
        } catch (\Exception $e) {
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
        foreach ($userFines as $fine) {
            $_fine = Fine::find($fine->fine_id);
            if ($_fine) {
                $amount = (int)$_fine->penalty_amount * $currency_rate;
                $totalFines += $amount;
                $amount = number_format($amount, 2, '.', ',');
                $fine->name = $_fine->name . '. Сумма: ' . $amount . ' ' . strtoupper($user->currency);
            } else {
                $fine->name = 'Добавлен штраф без ID. Сообщите в тех.поддержку';
            }
        }


        $userFines = $userFines->groupBy(function ($fine) {
            return Carbon::parse($fine->day)->format('d');
        });


        //bonuses
        $bonuses = Salary::where('user_id', $user->id)
            ->whereYear('date', date('Y'))
            ->whereMonth('date', $request->month)
            ->where('bonus', '!=', 0)
            ->orderBy('id', 'desc')
            ->get();

        $total_bonuses = $bonuses->sum('bonus');

        $bonuses = $bonuses->groupBy(function ($b) {
            return Carbon::parse($b->date)->format('d');
        });


        $total_bonuses += ObtainedBonus::onMonth($user->id, date('Y-m-d'));

        $obtained_bonuses = ObtainedBonus::where('user_id', $user->id)
            ->whereYear('date', date('Y'))
            ->whereMonth('date', $request->month)
            ->where('amount', '>', 0)
            ->get()
            ->groupBy(function ($b) {
                return Carbon::parse($b->date)->format('d');
            });

        $total_bonuses += TestBonus::where('user_id', $user->id)
            ->whereYear('date', date('Y'))
            ->whereMonth('date', $request->month)
            ->get()
            ->sum('amount');

        $test_bonus = TestBonus::where('user_id', $user->id)
            ->whereYear('date', date('Y'))
            ->whereMonth('date', $request->month)
            ->where('amount', '>', 0)
            ->get()
            ->groupBy(function ($b) {
                return Carbon::parse($b->date)->format('d');
            });
        // Бонусы

        $editedBonus = EditedBonus::where('user_id', $user->id)
            ->whereYear('date', date('Y'))
            ->whereMonth('date', $request->month)
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
            ->whereYear('date', date('Y'))
            ->whereMonth('date', $request->month)
            ->where('paid', '!=', 0)
            ->orderBy('id', 'desc')
            ->get();

        $total_avanses = $avanses->sum('paid');

        $avanses = $avanses->groupBy(function ($b) {
            return Carbon::parse($b->date)->format('d');
        });

        for ($i = 1; $i <= $date->daysInMonth; $i++) {
            $m = $i;
            if (strlen($i) == 1) $m = '0' . $i;
            $data['salaries'][$i]['fines'] = isset($userFines[$m]) ? $userFines[$m] : [];
            $data['salaries'][$i]['bonuses'] = isset($bonuses[$m]) ? $bonuses[$m] : [];
            $data['salaries'][$i]['awards'] = isset($obtained_bonuses[$m]) ? $obtained_bonuses[$m] : [];
            $data['salaries'][$i]['test_bonus'] = isset($test_bonus[$m]) ? $test_bonus[$m] : [];
            $data['salaries'][$i]['avanses'] = isset($avanses[$m]) ? $avanses[$m] : [];

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
            ->whereYear('date', $date->year)
            ->whereMonth('date', $date->month)
            ->whereIn('type', [5, 6, 7])
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

        for ($day = 1; $day <= $date->daysInMonth; $day++) {


            //count hourly pay

            $s = Salary::where('user_Id', $user->id)
                ->where('date', $date->day($day)->format('Y-m-d'))
                ->first();

            $zarplata = $s ? $s->amount : 70000;
            $working_hours = $user->workingTime ? $user->workingTime->time : 9;
            $ignore = $user->working_day_id == 1 ? [6, 0] : [0];   // Какие дни не учитывать в месяце
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

            if ($hour == '') {
                $hour = 0;
            }

            $data['salaries'][$day]['value'] = number_format(round($hour * $hourly_pay * $currency_rate), 0, '.', '');

            $data['salaries'][$day]['training'] = false;

            if ($trainee_days->where('datex', $day)->first()) {
                $hour = $user->working_time_id == 1 ? 8 : 9;
                $data['salaries'][$day]['value'] = number_format(round($hour * $hourly_pay * $currency_rate * $user->internshipPayRate()), 0, '.', ''); // стажировочные на пол суммы
                // $data['salaries'][$day]['value'] = 0;
                $data['salaries'][$day]['training'] = true;
            } else if ($tts_before_apply->where('date', $day)->first()) {
                $hour = $tts_before_apply->where('date', $day)->first()->total_hours / 60;
                $data['salaries'][$day]['value'] = number_format(round($hour * $hourly_pay * $currency_rate), 0, '.', '');
            }

            //if($tts_before_apply->where('date', $day)->first()) dd($tts_before_apply->where('date', $day)->first());

            $data['hours'][$day]['value'] = round($hour, 2);

            if ($data['salaries'][$day]['training'] || $data['hours'][$day]['value'] == 0) $data['hours'][$day]['value'] = '';
            if ($data['salaries'][$day]['value'] == 0) $data['salaries'][$day]['value'] = '';

            $data['salaries'][$day]['calculated'] = round($hour, 2) . ' * ' . ($trainee_days->where('datex', $day)->first() ? $hourly_pay * $user->internshipPayRate() : $hourly_pay);

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


    public function zarplatatableNew(Request $request)
    {
        $user = auth()->user() ?? User::find(5);

        $date = Carbon::createFromDate($request->input('year', date('Y')), $request->input('month'), 1);
        $currency_rate = (float)(Currency::rates()[$user->currency] ?? 0.00001);

        $userFinesInformation = $this->fineService->getUserFines($date->month, $user, $currency_rate);
        $salaryBonuses = $this->salaryService->getUserBonuses($date, $user);
        $obtainedBonuses = $this->obtainedBonusesService->getUserBonuses($date, $user);
        $testBonuses = $this->testBonusService->getUserBonuses($date, $user);
        $advances = $this->salaryService->getUserAdvances($date, $user);

        return (new TimetrackService())->getUserFinalSalary(
            $salaryBonuses,
            $obtainedBonuses,
            $testBonuses,
            $userFinesInformation['fines'],
            $userFinesInformation['total'],
            $advances,
            $user,
            $date,
            $currency_rate
        );
    }

    public function setDay(Request $request): array
    {
        /** @var User $user */
        $user = auth()->user();

        /** @var User $targetUser */
        $targetUser = User::withTrashed()->find($request->get('user_id'));

        if ($targetUser == null) return ['success' => 1, 'history' => null];

        $date = Carbon::parse($request->get('year') . '-' . $request->get("month") . '-' . $request->get('day'));

        /** @var DayType $daytype */
        $daytype = DayType::query()
            ->where('user_id', $request->get("user_id"))
            ->whereDate('date', $date->format('Y-m-d'))
            ->first();

        if (!$daytype) {
            $daytype = DayType::query()
                ->create([
                    'user_id' => $request->get("user_id"),
                    'type' => $request->get("type"),
                    'email' => $targetUser->email,
                    'date' => $date,
                    'admin_id' => $user->id,
                ]);
            $description = 'с обычного на ' . DayType::DAY_TYPES_RU[$request->get("type")];
        } else {
            $description = 'с ' . DayType::DAY_TYPES_RU[$daytype->type] . ' на ' . DayType::DAY_TYPES_RU[$request->get("type")];
            $daytype->type = $request->get("type");
            $daytype->admin_id = $user->id;
            $daytype->save();
        }

        $authorName = $user->name . ' ' . $user->last_name;
        $desc = isset($request['comment']) ? $description . '. Причина: ' . $request['comment'] : $description;

        $history = TimetrackingHistory::query()
            ->create([
                'user_id' => $request->get("user_id"),
                'author_id' => $user->id,
                'author' => $authorName,
                'date' => $date,
                'description' => $desc,
            ]);


        if ($request->get("type") == DayType::DAY_TYPES['HOLIDAY']) { // Выходной
            $fines = UserFine::query()
                ->where('day', $date)
                ->where('user_id', '=', $targetUser->id)
                ->get();

            foreach ($fines as $fine) {
                $fine->status = UserFine::STATUS_INACTIVE;
                $fine->save();
            }

            /** @var Salary $salary */
            $salary = Salary::query()
                ->where('date', $date)->where('user_id', $targetUser->id)->first();
            if ($salary) {
                $salary->amount = 0;
                $salary->save();
            }
        }

        if ($request->get("type") == DayType::DAY_TYPES['ABCENSE']) { // Отсутствует
            /** @var UserDescription $trainee */
            $trainee = UserDescription::query()
                ->where('is_trainee', 1)
                ->where('user_id', $request->get("user_id"))
                ->first();

            if ($trainee) {

                Referring::deleteReferrerDailySalary($targetUser->id, $date);

                $targetUser->salaries()->where('date', $date->format("Y-m-d"))->first()?->delete();

                $editPersonLink = 'https://' . tenant('id') . '.jobtron.org/timetracking/edit-person?id=' . $request->get("user_id");

                // Поиск ID лида или сделки
                if ($trainee->lead_id != 0) {
                    $lead_id = $trainee->lead_id;
                } else {
                    /** @var Lead $lead */
                    $lead = Lead::query()
                        ->where('phone', Phone::normalize($targetUser->phone))->orderBy('id', 'desc')->first();
                    if ($lead) {
                        $lead_id = $lead->lead_id;
                    } else {
                        $lead_id = 0;
                    }
                }

                /** @var Lead $lead */
                $lead = Lead::query()
                    ->where('user_id', $request->get("user_id"))->first();
                if ($lead) {
                    $lead->status = 'ABSENT';
                    $lead->save();
                }
                // Пропал с обучения

                $types = UserAbsenceCause::THIRD_DAY;
                $title_lost = 'Пропал с обучения';
                $notification_temp_id = 2;
                if (array_key_exists('1', $request->get("enable_comment")) && array_key_exists('2', $request->get("enable_comment"))) {
                    if ($request->get("day") == $request->get("enable_comment")['1']) {
                        $title_lost = 'Пропал с обучения: 1 день';
                        $notification_temp_id = 4;
                        $types = UserAbsenceCause::FIRST_DAY;
                    } else if ($request->get("day") == $request->get("enable_comment")['2']) {
                        $title_lost = 'Пропал с обучения: 2 день';
                        $notification_temp_id = 5;
                        $types = UserAbsenceCause::SECOND_DAY;
                    }
                }


                // Причина отсутствия 1 2 3 дни
                UserAbsenceCause::query()
                    ->updateOrCreate([
                        'user_id' => $request->get("user_id"),
                        'date' => $date->day(1)->format('Y-m-d'),
                        'type' => $types,
                        'text' => $request->get("comment"),
                    ]);


                //
                $notions = UserNotification::query()
                    ->where([
                        'title' => $title_lost,
                        'about_id' => $targetUser->id,
                    ])->get();

                foreach ($notions as $not) {
                    $not->read_at = now();
                    $not->save();
                }

                ////////// notify
                /** @var ProfileGroup $g */
                $g = ProfileGroup::query()->find($request->get("group_id"));
                $group_name = $g ? '(' . $g->name . ')' : '';

                $abs_msg = $authorName . ': ' . $group_name . '  Стажер не был на обучении: <br> <a href="' . $editPersonLink . '" target="_blank">';
                $abs_msg .= $targetUser->last_name . ' ' . $targetUser->name . ' </a>';
                $abs_msg .= '<br><a href="/timetracking/analytics/skypes/' . $lead_id . '" target="_blank" class="btn btn-primary mr-2 mt-2 rounded btn-sm">Перейти в сделку</a>';
                $abs_msg .= '<a class="btn btn-primary mt-2 rounded btn-sm transfer-training" data-userid="' . $targetUser->id . '">Перенести обучение</a>';

                $timestamp = now();

                $notification_receivers = NotificationTemplate::getReceivers($notification_temp_id);

                foreach ($notification_receivers as $user_id) {
                    if ($user_id == 3460) {
                        if ($g->id == 42 || $g->id == 88) {
                            UserNotification::query()
                                ->create([
                                    'user_id' => $user_id,
                                    'about_id' => $targetUser->id,
                                    'title' => $title_lost,
                                    'message' => $abs_msg,
                                    'group' => $timestamp
                                ]);
                        }
                    } else {
                        UserNotification::query()
                            ->create([
                                'user_id' => $user_id,
                                'about_id' => $targetUser->id,
                                'title' => $title_lost,
                                'message' => $abs_msg,
                                'group' => $timestamp
                            ]);
                    }

                }

                ///////// // перенос сделки Обучается на Пропал с обучения в БИТРИКС

                $bitrix = new Bitrix();

                if ($trainee->deal_id != 0) {
                    $deal_id = $trainee->deal_id;
                } else if ($lead_id != 0) {
                    $deal_id = $bitrix->findDeal($lead_id, false);
                    usleep(1000000); // 1 sec
                } else {
                    $deal_id = 0;
                }

                if ($deal_id != 0) {
                    $bitrix->changeDeal($deal_id, [
                        'STAGE_ID' => 'C4:21'
                    ]);
                }
                /////-*-*-*-----------*-*-*-*-*-*-*//
            }


            if (in_array($date->dayOfWeek, [0, 1])) {
                $kaspi35 = json_decode(ProfileGroup::query()->find(35)->users);
                $kaspi42 = json_decode(ProfileGroup::query()->find(42)->users);
                $kaspi = array_merge($kaspi35, $kaspi42);

                if (in_array($request->get("user_id"), $kaspi)) {
                    /** @var UserFine $fine */
                    $fine = UserFine::query()
                        ->where('user_id', $request->get("user_id"))
                        ->whereDate('day', $date->format('Y-m-d'))
                        ->where('fine_id', 53)
                        ->first();

                    if ($fine) {
                        $fine->status = 1;
                        $fine->save();
                    } else {
                        $userFine = new UserFine;
                        $userFine->user_id = $request->get("user_id");
                        $userFine->fine_id = 53;
                        $userFine->status = 1;
                        $userFine->day = $date;
                        $userFine->note = '';
                        $userFine->save();
                    }
                }

            }
            $up = UserPresence::query()
                ->where('date', $date)
                ->where('user_id', $request->get("user_id"))
                ->first();
            $up?->delete();
        }

        if ($request->get("type") == DayType::DAY_TYPES['TRAINEE']) {
            $trainee = UserDescription::query()
                ->where('is_trainee', 1)->where('user_id', $request->get("user_id"))->first();
//            DayType::markDayAsTrainee($targetUser, $date); // It is already created in start of function
            UserPresence::query()
                ->firstOrCreate([
                    'date' => $date,
                    'user_id' => $request->get("user_id")
                ]);
            Referring::touchReferrerSalaryForTrain($targetUser, $date);

            if ($trainee) {
                $bitrix = new Bitrix();

                $deal_id = 0;


                if ($trainee->deal_id != 0) {
                    $deal_id = $trainee->deal_id;
                } else if ($trainee->lead_id != 0) {
                    $deal_id = $bitrix->findDeal($trainee->lead_id, false);
                    usleep(1000000); // 1 sec
                }

                if ($deal_id != 0) {
                    $bitrix->changeDeal($deal_id, [
                        'STAGE_ID' => 'C4:18'
                    ]);
                }
            }
        }
        if ($request->get("type") == DayType::DAY_TYPES['DEFAULT']) {
            $salaryForTomorrow = Salary::query()
                ->where('date', $date->subDay())->where('user_id', $targetUser->id)->first();
            /** @var Salary $salary */
            $salary = Salary::query()
                ->where('date', $date)
                ->where('user_id', $targetUser->id)
                ->first();
            dd($salaryForTomorrow, $salary);
            if ($salaryForTomorrow && $salary && (int)$salary->amount == 0) {
                $salary->amount = $salaryForTomorrow->amount;
                $salary->save();
            }
        }

        if ($request->get("type") == DayType::DAY_TYPES['FIRED']) { // Уволенный сотрудник DayType::DAY_TYPES['ABCENSE']
            /** @var UserDescription $trainee */
            $trainee = UserDescription::query()
                ->where('is_trainee', 1)->where('user_id', $request->get("user_id"))->first();

            if ($trainee) {
                Referring::deleteReferrerDailySalary($targetUser->getKey(), $date);

                // Поиск ID лида или сделки
                if ($trainee->lead_id != 0) {
                    $lead_id = $trainee->lead_id;
                } else {
                    /** @var Lead $lead */
                    $lead = Lead::query()
                        ->where('phone', $targetUser->phone)->orderBy('id', 'desc')->first();
                    if ($lead) {
                        $lead_id = $lead->lead_id;
                        $lead->update(['status' => 'LOSE']);
                    } else {
                        $lead_id = 0;
                    }
                }

                ///////// // перенос сделки в статус Отказ от Вакансии

                $bitrix = new Bitrix();

                if ($trainee->deal_id != 0) {
                    $deal_id = $trainee->deal_id;
                } else if ($lead_id != 0) {
                    $deal_id = $bitrix->findDeal($lead_id, false);
                    usleep(1000000); // 1 sec
                } else {
                    $deal_id = 0;
                }

                if ($deal_id != 0) {
                    $bitrix->changeDeal($deal_id, [
                        'STAGE_ID' => 'C4:LOSE'
                    ]);
                }

                //
                $trainee->fired = now();
                $trainee->save();

                UserDescription::query()->make([
                    'user_id' => $request->get("user_id"),
                    'fired' => now(),
                    'fire_cause' => $request->get("comment")
                ]);

                ////////////
                User::deleteUser($request);
            } else {
                UserDescription::query()->make([
                    'user_id' => $request->get("user_id"),
                    'fired' => now(),
                    'fire_cause' => $request->get("comment")
                ]);

                if ($request->get("fire_type") == 1) { // Без отработки
                    $delete_plan = UserDeletePlan::query()
                        ->where('user_id', $request->get("user_id"))->orderBy('id', 'desc')->first();
                    $delete_plan?->delete();
                    User::deleteUser($request);
                } else { //отработкой
                    if ($request->hasFile('file')) { // Заявление об увольнении
                        $file = $request->file('file');
                        $resignation = $targetUser->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                        $file->move("static/profiles/" . $targetUser->id . "/resignation", $resignation);

                        $downloads = Downloads::query()
                            ->where('user_id', $targetUser->id)->first();
                        if ($downloads) {
                            $downloads->resignation = $resignation;
                            $downloads->save();
                        } else {
                            Downloads::query()
                                ->create([
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

                    $fire_date = Carbon::now()->addHours(24 * 14); // fire after week 2

                    UserDeletePlan::query()->updateOrCreate(
                        [
                            'user_id' => $request->get("user_id")
                        ], [
                        'executed' => 0,
                        'delete_time' => $fire_date,
                    ]);
                }
            }


            if ($request->get("comment")) {

                if ($trainee) {
                    // Причина отсутствия 1 2 3 дни
                    $type = UserAbsenceCause::THIRD_DAY;
                    /** @var Lead $lead */
                    $lead = Lead::query()
                        ->where('user_id', $request->get("user_id"))->first();

                    if ($lead) {
                        if ($date->format('Y-m-d') == Carbon::parse($lead->invite_at)->format('Y-m-d')) {
                            $type = UserAbsenceCause::FIRST_DAY;
                        } else if ($date->format('Y-m-d') == Carbon::parse($lead->day_second)->format('Y-m-d')) {
                            $type = UserAbsenceCause::SECOND_DAY;
                        }
                    }

                    UserAbsenceCause::query()
                        ->updateOrCreate([
                            'user_id' => $request->get("user_id"),
                            'date' => $date->day(1)->format('Y-m-d'),
                            'type' => $type,
                            'text' => $request->get("comment"),
                        ]);
                }

            }
            Referring::touchReferrerStatus($targetUser);
        }

        return ['success' => 1, 'history' => $history, 'type' => $daytype ? $daytype->type : 0];
    }

    public function getTotalsOfReports(Request $request)
    {

        $x_users = [];
        $group = ProfileGroup::find($request->group_id);
        if (!empty($group) && $group->users != null) {
            $x_users = json_decode($group->users);
        }

        $users_ids = User::whereIn('id', $x_users)->where('position_id', 32)->pluck('id')->toArray();

        $sum = Timetracking::getSumHoursPerMonthByUsersIds($users_ids, $request->month, $request->year);

        foreach ($sum as $key => $value) {
            $sum[$key] = number_format((float)$value / 9, 2, '.', '');
        }

        return response()->json([
            'sum' => $sum
        ]);
    }

    public function getUserNotifications(Request $request)
    {

        $user = User::withTrashed()
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where('ud.notifications', '!=', '[]')
            ->where('users.id', $request->user_id)
            ->select(DB::raw("CONCAT_WS(' ',users.id, users.last_name, users.name) as name"), 'users.id as id', 'ud.notifications as notifications')
            ->first();

        $notifications = [];

        if ($user) {
            foreach (json_decode($user->notifications) as $noti) {

                $template = NotificationTemplate::where('id', $noti[0])
                    ->select('id', 'title', 'message', 'need_group')
                    ->first();

                if ($template) {
                    $_groups = [];

                    $noti[0] = [
                        'id' => $template->id,
                        'title' => $template->title,
                    ];
                    foreach ($noti[1] as $group_id) {
                        $group = ProfileGroup::find($group_id);

                        if ($group) {
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

    public function saveUserNotifications(Request $request)
    {

        $array = [];

        $left_noti = [];

        foreach ($request->noti as $noti) {
            try {
                $item = [];
                $item[0] = $noti[0]['id'];
                $item[1] = [];

                foreach ($noti[1] as $group) {
                    array_push($item[1], $group['id']);
                }

                array_push($array, $item);
                array_push($left_noti, $noti[0]['id']);

                $template = NotificationTemplate::find($noti[0]['id']);
                if ($template) {
                    $ids = json_decode($template->ids);
                    array_push($ids, $request->user_id);
                    $ids = array_unique($ids);
                    $template->ids = json_encode($ids);
                    $template->save();
                }

            } catch (\Exception $e) {
                continue;
            }
        }

        $ud = UserDescription::where('user_id', $request->user_id)->first();

        if ($ud) {
            $notifications = $ud->notifications;
            if ($notifications == null) $notifications = [];
            foreach ($notifications as $noti) {
                if (!in_array($noti[0], $left_noti)) {
                    $template = NotificationTemplate::find($noti[0]);
                    if ($template) {

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
                'notifications' => $array
            ]);
        }


    }

    public function getTimeAddresses(Request $request)
    {
        $group = ProfileGroup::find($request->group_id);

        $time_variants = [
            '-1' => 'Из U-calls',
            '0' => 'Не выбран',
        ];
        $time_exceptions_options = [];
        $time_exceptions = [];

        if ($group) {
            $activities = Activity::where('group_id', $group->id)->where('type', 'default')->get();
            foreach ($activities as $key => $activity) {
                $time_variants[$activity->id] = $activity->name;
            }

            if ($group->users != null) {
                $time_exceptions_options = \DB::table('users')
                    ->whereNull('deleted_at')
                    ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                    ->where('ud.is_trainee', 0)
                    ->whereIn('users.id', json_decode($group->users))
                    ->get(['users.id', DB::raw("CONCAT(name,' ',last_name,'-',email) as email")]);
            }

            if (!isset($group->time_exceptions)) {
                $time_exceptions = [];
            } else {
                $time_exceptions = \DB::table('users')
                    ->whereNull('deleted_at')
                    ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                    ->where('ud.is_trainee', 0)
                    ->whereIn('users.id', $group->time_exceptions)
                    ->get(['users.id', DB::raw("CONCAT(name,' ',last_name,'-',email) as email")]);
            }
        }


        return [
            'time_variants' => $time_variants,
            'time_exceptions_options' => $time_exceptions_options,
            'time_exceptions' => $time_exceptions,
        ];
    }

    public function saveTimeAddresses(Request $request)
    {
        $group = ProfileGroup::where('id', $request->group_id)
            ->first();

        if ($group) {
            $group->time_address = $request->time_address;

            $users = [];
            foreach ($request->time_exceptions as $time_exception) {
                $users[] = $time_exception['id'];
            }
            $group->time_exceptions = $users;
            $group->save();
        }
    }

    public function restoreGroup(Request $request)
    {
        $group = ProfileGroup::where('id', $request->id)->first();

        if ($group) {
            $group->active = 1;
            $group->archived_date = null;
            $group->save();
        }
    }

    public function deleteGroupBonus(Request $request)
    {
        $bonus = Bonus::where('id', $request->id)->first();
        if ($bonus) $bonus->delete();
    }

    private function insertDataToGroupUser($group, $usersId)
    {
        $data = [];


        foreach ($usersId as $userId) {
            $exist = DB::table('group_user')
                ->where('user_id', $userId)
                ->where('group_id', $group->id)
                ->exists();

            if (!$exist) {
                $data[] = [
                    'user_id' => $userId,
                    'group_id' => $group->id,
                    'from' => Carbon::now()->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }

        DB::table('group_user')->insert($data);
    }

    /**
     * Получем массив user-ов и добавляем в таблицу group_user
     */
    public function addUsers(Request $request, GroupUserService $groupUserService)
    {
        $response = $groupUserService->save($request);

        return response()->json($response);
    }

    /**
     * Удаляет массив юзеров.
     * @param Request $request
     * @param GroupUserService $groupUserService
     * @return JsonResponse
     */
    public function dropUsers(Request $request, GroupUserService $groupUserService): JsonResponse
    {
        $response = $groupUserService->drop($request->users, $request->group_id);

        return response()->json($response);
    }

    /**
     * @param $user
     * @return void
     */
    private function addHours($user): void
    {
        foreach ($user->trackHistory as $history) {
            $history->created_at = $history->created_at->addHours(6)->format('Y-m-d H:i:s');
            $history->updated_at = $history->updated_at->addHours(6)->format('Y-m-d H:i:s');
        }
    }

    /**
     * Отправка заявку на сверхурочную работу
     * @param OvertimeService $service
     * @param OvertimeRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function overtime(OvertimeService $service, OvertimeRequest $request)
    {
        $response = $service->handle($request->validated());

        return $this->response(
            message: 'Successfully send notification',
            data: $response
        );
    }

    public function accept(AcceptOvertimeRequest $request, AcceptOvertimeService $service)
    {
        $data = $request->validated();
        $response = $service->handle($data);
        if ($response) {
            $this->startDay($data['user_id']);

            return $this->response(
                message: 'Successfully accepted',
                data: $response
            );
        }

        return $this->response(
            message: 'Notifications not send',
            data: false
        );
    }

    public function reject(RejectOvertimeRequest $request, RejectOvertimeService $service)
    {
        $response = $service->handle($request->validated());

        return $this->response(
            message: 'Successfully rejected',
            data: $response
        );
    }
}
