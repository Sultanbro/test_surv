<?php

namespace App\Http\Controllers\Analytics;

use App\Api\BitrixOld as Bitrix;
use App\CallBase;
use App\Classes\Analytics\FunnelTable;
use App\Classes\Analytics\Recruiting as RM;
use App\Classes\Helpers\Phone;
use App\DayType;
use App\Http\Controllers\Controller;
use App\Models\Analytics\Activity;
use App\Models\Analytics\DecompositionValue;
use App\Models\Analytics\RecruiterStat;
use App\Models\Analytics\TraineeReport;
use App\Models\Bitrix\Lead;
use App\Models\Bitrix\Segment;
use App\Models\GroupUser;
use App\Models\User\NotificationTemplate;
use App\ProfileGroup;
use App\Service\SendMessageTraineesService;
use App\Service\Tenancy\CabinetService;
use App\User;
use App\UserNotification;
use App\Zarplata;
use Carbon\Carbon;
use Closure;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use View;

class HrController extends Controller
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
        $this->middleware(function (Request $request, Closure $next) {
            if (!auth()->user()->can('hr_view')) {
                return $this->response('Access denied.', [], '403');
            }
            return $next($request);
        })->only(
            'recrutingAnalytics', 'getRecruitmentStatictics', 'getSynoptics', 'getTrainees',
            'getInternshipStagesSynoptics', 'getInternshipStagesAbsents', 'getFunnel', 'getDismissStatistics',
            'getDismissBot', 'getDismissReasons'
        );
    }


    /**
     * Страница аналитика группы
     */
    public function index()
    {
        if (!auth()->user()->can('hr_view') && tenant('id') != 'bp') {
            return redirect('/');
        }

        $currentUser = auth()->user();

        $recruting = ProfileGroup::find(48);

        // Доступ к группе

        $groups = [];

        if ($recruting && auth()->user()->can('hr_view')) {
            $groups[] = [
                'id' => 48,
                'name' => $recruting->name,
                'groups' => [48],
            ];
        }


        $groups = collect($groups);

        View::share('menu', 'timetracking_hr');
        return view('admin.analytics', compact('groups'));
    }

    /**
     *
     */
    public function saveCallBase(Request $request)
    {
        CallBase::saveTable([
            'total' => $request->total,
            'conversion' => $request->conversion,
            'current_credits' => $request->current_credits,
            'next_credits' => $request->next_credits,
            'current_given' => $request->current_given,
            'next_given' => $request->next_given,
        ], $request->date);
    }

    /**
     * Аналитика РЕКРУТИНГА
     * @param Request $request
     * @return array
     */
    public function recrutmentAnalytics(Request $request)
    {
        $month = Carbon::createFromFormat('m-Y', $request->month . '-' . $request->year)->startOfMonth();
        $date = [
            'month' => $request->month,
            'year' => $request->year,
        ];

        $settings = RM::getSummaryTable($month);
        $data = $settings ? $settings->data : RM::defaultSummaryTable();
        $absence_causes = RM::getAbsenceCauses($date); // Причины отсутствия на 1 и 2 день стажировки

        $trainees = DayType::whereYear('date', $request->year) // Стажеры
        ->whereMonth('date', $request->month)
            ->whereDay('date', RM::getLastDay($month))
            ->whereIn('type', [5, 7])
            ->select('user_id')
            ->distinct('user_id')
            ->count('user_id');

        $recruiter_stats_rates = [];
        for ($i = 1; $i <= $month->daysInMonth; $i++) {
            $recruiter_stats_rates[$i] = $settings && array_key_exists($i, $data[RM::S_ONLINE]) ? $data[RM::S_ONLINE][$i] : 0;
        }

        $remain_apply = $data[RM::S_APPLIED]['plan'] - $data[RM::S_APPLIED]['fact'];

        $indicators = [];
        $indicators['info']['trainees'] = $data[RM::S_CONVERTED]['fact'] > 0 ? $trainees . ' - ' . round($trainees / $data[RM::S_CONVERTED]['fact'] * 100) . '%' : 0; // Cтажировались в этом месяце
        $indicators['info']['training'] = $trainees; // Cтажируются сегодня
        $indicators['info']['applied'] = $data[RM::S_CONVERTED]['fact'] > 0 ? $data[RM::S_APPLIED]['fact'] . ' - ' . round($data[RM::S_APPLIED]['fact'] / $data[RM::S_CONVERTED]['fact'] * 100) . '%' : 0; // Принято сотрудников
        $indicators['info']['remain_apply'] = $remain_apply > 0 ? $remain_apply : 0; // Осталось аринять
        $indicators['info']['created'] = $data[RM::S_CREATED]['fact']; // Создано лидов
        $indicators['info']['processed'] = $data[RM::S_CREATED]['fact'] > 0 ? $data[RM::S_PROCESSED]['fact'] . ' - ' . round($data[RM::S_PROCESSED]['fact'] / $data[RM::S_CREATED]['fact'] * 100) . '%' : 0; // Обработано лидов
        $indicators['info']['converted'] = $data[RM::S_CREATED]['fact'] > 0 ? $data[RM::S_CONVERTED]['fact'] . ' - ' . round($data[RM::S_CONVERTED]['fact'] / $data[RM::S_CREATED]['fact'] * 100) . '%' : 0; // Сконвертировано лидов
        $indicators['info']['fired'] = $data[RM::S_FIRED]['fact'];  // Увоолено в этом месяце
        $indicators['info']['applied_plan'] = $data[RM::S_APPLIED]['plan'];// План по принятию на штат на месяц
        $indicators['info']['remain_days'] = RM::daysRemain($date); // Осталось рабочих дней до конца месяца
        $indicators['info']['working'] = $settings && $settings->extra && array_key_exists('working', $settings->extra) ? $settings->extra['working'] : RM::getWorkerQuantity(); // Кол-во работающих (Ставка)
        $indicators['recruiters'] = []; // Для графической аналитики
        $indicators['orders'] = RM::getOrders(); // Заказы стажеров от руководителей
        $indicators['today'] = date('d');
        $indicators['month'] = $request->month;

        $groups = ProfileGroup::where('active', 1)->get();

        $inviteGroups = $groups->pluck('name', 'id')->toArray();
        $inviteGroups[0] = 'Все группы';

        return [
            //Method - getRecruitmentStatictics. Стасистика рекрутеров по 31 дням
            'recruiter_stats' => RecruiterStat::tables($month->startOfMonth()->format('Y-m-d')), // Почасовая таблица на главной
            'recruiter_stats_rates' => $recruiter_stats_rates, // Кол-во рекрутеров (Ставка)
            'recruiter_stats_leads' => RecruiterStat::leads($month->startOfMonth()->format('Y-m-d')), // Кол-во лидов битрикс в статусе "В работе"

            //Method - getSynoptics. Сводная
            'date' => $month->startOfMonth()->format('Y-m-d'),
            'records' => $data, // Сводная таблица
            'hrs' => [], // Подробные таблицы рекрутеров
            'indicators' => $indicators, // Разные показатели на главной

            //Method - getTrainees. Стажеры
            'skypes' => Lead::fetch($date), // Cконвертированные сделки. Раньше собирали скайпы (Нужно переименовать)
            'invite_groups' => $inviteGroups, // Фильтр для таблицы "стажеры"
            'segments' => Segment::pluck('name', 'id'), // Cегменты
            'sgroups' => $groups, // Группы для приглашения

            //Method - getInternshipStagesSynoptics. этап стажировки - 1. Сводная
            'ocenka_svod' => RM::ocenka_svod($month->startOfMonth()), // Анкета уволенных // 4.1 sec

            //Method - getInternshipStagesAbsents. этап стажировки - 3 .Отсутствие стажеров
            'absents_first' => $absence_causes['first_day'],
            'absents_second' => $absence_causes['second_day'],
            'absents_third' => $absence_causes['third_day'],

            //Method - getFunnel. Воронка
            'funnels' => FunnelTable::getTables($month->startOfMonth()->format('Y-m-d')), // Воронки

            //Method - getDismissStatistics. IV Увольнение - 1. Причины и процент текучки
            'staff' => RM::staff($request->year), // Таблица кадров во вкладке причина увольнения
            'staff_by_group' => RM::staff_by_group($request->year), // Таблица кадров во вкладке причина увольнения // 5.2 sec
            'staff_longevity' => RM::staff_longevity($request->year), // Таблица кадров во вкладке причина увольнения

            //Method - getDismissBot. IV Увольнение - 2. Причины: Бот
            'quiz' => RM::getQuizTable($month->startOfMonth()), // Анкета уволенных

            //Method - getDismissReasons. IV Увольнение - 3. Причины увольнения
            'causes' => RM::fireCauses($date), // причины увольнения

            'ratings' => RM::ratingsGroups($date), // Оценки операторов по Отделам
            'ratings_dates' => RM::ratingsDates($date), // Оценки операторов по датам
            'ratings_heads' => [], // UserDescription::getHeadsRatings($month->startOfMonth()), // Оценки операторов по руководителям // 12.2 sec
            'decomposition' => DecompositionValue::table($request->group_id, $month->format('Y-m-d')),
            'trainee_report' => TraineeReport::getBlocks($month->format('Y-m-d')), // оценки первого дня и присутствие стажеров
            'workdays' => ProfileGroup::find(48)->workdays,
            'trainee_participation' => 'testing'
        ];
    }

    /**
     * Статистика по рекрутерам
     * @param Request $request
     * @return array
     */
    public function getRecruitmentStatictics(Request $request)
    {
        $date = Carbon::createFromFormat('d-m-Y', $request->day . '-' . $request->month . '-' . $request->year);

        $settings = RM::getSummaryTable($date);

        $data = $settings ? $settings->data : RM::defaultSummaryTable();

        $recruiter_stats_rates = [];
        for ($i = 1; $i <= $date->daysInMonth; $i++) {
            $recruiter_stats_rates[$i] = $settings && array_key_exists($i, $data[RM::S_ONLINE]) ? $data[RM::S_ONLINE][$i] : 0;
        }

        $date = $date->format('Y-m-d');

        return [
            'date' => $date,
            'recruiter_stats' => RecruiterStat::tablesPerDay($date), // Почасовая таблица на главной
            'recruiter_stats_rates' => $recruiter_stats_rates, // Кол-во рекрутеров (Ставка)
            'recruiter_stats_leads' => RecruiterStat::leadsPerDay($date), // Кол-во лидов битрикс в статусе "В работе"
        ];
    }

    /**
     * Возвращает сводные данные
     * @param Request $request
     * @return array
     */
    public function getSynoptics(Request $request)
    {
        $month = Carbon::createFromFormat('m-Y', $request->month . '-' . $request->year)->startOfMonth();
        $date = [
            'month' => $request->month,
            'year' => $request->year,
        ];

        $settings = RM::getSummaryTable($month);
        $data = $settings ? $settings->data : RM::defaultSummaryTable();

        $trainees = DayType::whereYear('date', $request->year) // Стажеры
        ->whereMonth('date', $request->month)
            ->whereDay('date', RM::getLastDay($month))
            ->whereIn('type', [5, 7])
            ->select('user_id')
            ->distinct('user_id')
            ->count('user_id');

        $recruiter_stats_rates = [];
        for ($i = 1; $i <= $month->daysInMonth; $i++) {
            $recruiter_stats_rates[$i] = $settings && array_key_exists($i, $data[RM::S_ONLINE]) ? $data[RM::S_ONLINE][$i] : 0;
        }

        $remain_apply = $data[RM::S_APPLIED]['plan'] - $data[RM::S_APPLIED]['fact'];

        $indicators = [];
        $indicators['info']['trainees'] = $data[RM::S_CONVERTED]['fact'] > 0 ? $trainees . ' - ' . round($trainees / $data[RM::S_CONVERTED]['fact'] * 100) . '%' : 0; // Cтажировались в этом месяце
        $indicators['info']['training'] = $trainees; // Cтажируются сегодня
        $indicators['info']['applied'] = $data[RM::S_CONVERTED]['fact'] > 0 ? $data[RM::S_APPLIED]['fact'] . ' - ' . round($data[RM::S_APPLIED]['fact'] / $data[RM::S_CONVERTED]['fact'] * 100) . '%' : 0; // Принято сотрудников
        $indicators['info']['remain_apply'] = $remain_apply > 0 ? $remain_apply : 0; // Осталось аринять
        $indicators['info']['created'] = $data[RM::S_CREATED]['fact']; // Создано лидов
        $indicators['info']['processed'] = $data[RM::S_CREATED]['fact'] > 0 ? $data[RM::S_PROCESSED]['fact'] . ' - ' . round($data[RM::S_PROCESSED]['fact'] / $data[RM::S_CREATED]['fact'] * 100) . '%' : 0; // Обработано лидов
        $indicators['info']['converted'] = $data[RM::S_CREATED]['fact'] > 0 ? $data[RM::S_CONVERTED]['fact'] . ' - ' . round($data[RM::S_CONVERTED]['fact'] / $data[RM::S_CREATED]['fact'] * 100) . '%' : 0; // Сконвертировано лидов
        $indicators['info']['fired'] = $data[RM::S_FIRED]['fact'];  // Увоолено в этом месяце
        $indicators['info']['applied_plan'] = $data[RM::S_APPLIED]['plan'];// План по принятию на штат на месяц
        $indicators['info']['remain_days'] = RM::daysRemain($date); // Осталось рабочих дней до конца месяца
        $indicators['info']['working'] = $settings && $settings->extra && array_key_exists('working', $settings->extra) ? $settings->extra['working'] : RM::getWorkerQuantity(); // Кол-во работающих (Ставка)
        $indicators['recruiters'] = []; // Для графической аналитики
        $indicators['orders'] = RM::getOrders(); // Заказы стажеров от руководителей
        $indicators['today'] = date('d');
        $indicators['month'] = $request->month;

        $groups = ProfileGroup::where('active', 1)->get();

        $inviteGroups = $groups->pluck('name', 'id')->toArray();
        $inviteGroups[0] = 'Все группы';

        return [
            'date' => $month->startOfMonth()->format('Y-m-d'),
            'records' => $data, // Сводная таблица
            'hrs' => [], // Подробные таблицы рекрутеров
            'indicators' => $indicators, // Разные показатели на главной
        ];
    }

    /**
     * Возвращает список стажеров
     * @param Request $request
     * @return array
     */
    public function getTrainees(Request $request): array
    {
        $month = Carbon::createFromFormat('m-Y', $request->month . '-' . $request->year)->startOfMonth();
        $date = [
            'month' => $request->month,
            'year' => $request->year,
            'limit' => $request->limit ? $request->limit : 40,
        ];

        $settings = RM::getSummaryTable($month);
        $data = $settings ? $settings->data : RM::defaultSummaryTable();

        $recruiter_stats_rates = [];
        for ($i = 1; $i <= $month->daysInMonth; $i++) {
            $recruiter_stats_rates[$i] = $settings && array_key_exists($i, $data[RM::S_ONLINE]) ? $data[RM::S_ONLINE][$i] : 0;
        }

        $groups = ProfileGroup::where('active', 1)->get();

        $inviteGroups = $groups->pluck('name', 'id')->toArray();
        $inviteGroups[0] = 'Все группы';

        return [
            'skypes' => Lead::fetchWithPagination($date), // Cконвертированные сделки. Раньше собирали скайпы (Нужно переименовать)
            'invite_groups' => $inviteGroups, // Фильтр для таблицы "стажеры"
            'segments' => Segment::pluck('name', 'id'), // Cегменты
            'sgroups' => $groups, // Группы для приглашения
        ];
    }

    /**
     * Возвращает данные 2 этапа стажировки
     * @param Request $request
     * @return array
     */
    public function getInternshipSecondStage(Request $request)
    {
        $date = [
            'month' => $request->month,
            'year' => $request->year,
        ];

        $absence_causes = RM::getAbsenceCauses($date); // Причины отсутствия на 1 и 2 день стажировки
        $month = Carbon::createFromFormat('m-Y', $request->month . '-' . $request->year)->startOfMonth();

        return [
            'ocenka_svod' => RM::ocenka_svod($month->startOfMonth()), // Анкета уволенных // 4.1 sec
            'absents_first' => $absence_causes['first_day'],
            'absents_second' => $absence_causes['second_day'],
            'absents_third' => $absence_causes['third_day'],
            'trainee_report' => TraineeReport::getBlocks($month->format('Y-m-d')), // оценки первого дня и присутствие стажеров
        ];
    }

    /**
     * Возвращает воронку
     * @param Request $request
     * @return array
     */
    public function getFunnel(Request $request)
    {
        $month = Carbon::createFromFormat('m-Y', $request->month . '-' . $request->year);

        return [
            'funnels' => FunnelTable::getTables($month->startOfMonth()->format('Y-m-d')), // Воронки
        ];
    }

    /**
     * Возвращает статистику увольнений
     * @param Request $request
     * @return array
     */
    public function getDismissStatistics(Request $request)
    {
        $date = [
            'month' => $request->month,
            'year' => $request->year,
        ];
        $month = Carbon::createFromFormat('m-Y', $request->month . '-' . $request->year)->startOfMonth();
        return [
            'staff' => RM::staff($request->year), // Таблица кадров во вкладке причина увольнения
            'staff_by_group' => RM::staff_by_group($request->year), // Таблица кадров во вкладке причина увольнения // 5.2 sec
            'staff_longevity' => RM::staff_longevity($request->year), // Таблица кадров во вкладке причина увольнения
            'quiz' => RM::getQuizTable($month->startOfMonth()),
            'causes' => RM::fireCauses($date), // причины увольнения
        ];
    }

    /**
     * Перенаправление на битрикс сделку по lead_id
     * @int $id - lead_id в битриксе
     * $x, $y - артефакты роутера (НУЖНО УБРАТЬ)
     */
    public function redirectToBitrixDeal($id)
    {
        $lead = Lead::where('lead_id', $id)->first();

        $deal_id = 0;

        if ($lead) {

            if ($lead->deal_id == 0) {
                $bitrix = new Bitrix();
                $deal_id = $bitrix->findDeal($lead->lead_id, false);
            } else {
                $deal_id = $lead->deal_id;
            }

        }

        if ($deal_id == 0) {
            return redirect('https://infinitys.bitrix24.kz/crm/lead/details/' . $id . '/');
        } else {
            return redirect('https://infinitys.bitrix24.kz/crm/deal/details/' . $deal_id . '/');
        }

        // if(ob_get_length() > 0) ob_clean(); //  ob_end_clean();
    }

    /**
     * Пригласить на стажировку во вкладке Аналитика Групп (Рекрутинг) - Стажеры
     * Создает пользователей и меняет сделку в битриксе
     */
    public function inviteUsers(Request $request, SendMessageTraineesService $service)
    {
        $userIds = [];
        $leads = Lead::query()
            ->whereIn('id', $request->users)
            ->get();

        /////////// check group and zoom link existence
        $group = ProfileGroup::query()
            ->find($request['group_id']);

        if (!$group) {
            return [
                'code' => 201
            ];
        }

        ////
        if ($request->time) {
            $hour = substr($request->time, 0, 2);
            $minute = substr($request->time, 3, 2);
            $invite_at = Carbon::parse($request->date)->hour($hour)->minute($minute);
        } else {
            $invite_at = Carbon::parse($request->date);
        }

        $day_second = Carbon::parse($request->date)->addDays(1);
        if ($day_second->dayOfWeek == 6) $day_second->addDays(2);
        if ($day_second->dayOfWeek == 0) $day_second->addDays(1);

        $msg_for_group_leader = '';

        $has_remote_to_send_notification = false;

        /**
         * leads
         */
        foreach ($leads as $lead) {

            // Проверить существует ли user


            $original_password = User::generateRandomString();
            $salt = User::randString(8);
            $user_password = $salt . md5($salt . $original_password);


            if ($lead->email) {
                $email = $lead->email;
            } else {
                $email = 'user' . $lead->lead_id . '@bpartners.kz';
                if ($lead->status == 'MAN') {
                    $email = 'person' . $lead->id . '@bpartners.kz';
                }
            }

            try {
                if (in_array($lead->wishtime, [4, 5, 6])) {
                    $full_time = 0;
                } else {
                    $full_time = 1;
                }
            } catch (\Exception $e) {
                $full_time = 1;
            }

            /** @var User $user */
            $user = User::withTrashed()->where('email', $email)->first();

            $currency = Phone::getCurrency($lead->phone);
            $user_type = $lead->skyped ? 'remote' : 'office';

            if ($user_type == 'remote') {
                $has_remote_to_send_notification = true;
            }

            $uname = strlen($lead->name) > 50 ? mb_substr($lead->name, 0, 49) : $lead->name;
            if (!$user) {
                /** @var User $user */
                $user = User::query()->create([
                    'email' => $email,
                    'name' => $uname,
                    'last_name' => '',
                    'description' => $email,
                    'password' => \Hash::make('12345'),
                    'position_id' => 32, // Оператор
                    'user_type' => $user_type,
                    'timezone' => 6,
                    'birthday' => now(),
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
                    'is_admin' => 0,
                    'referrer_id' => $lead->referrer_id,
                ]);

                (new CabinetService)->add(tenant('id'), $user, false);

                $user->user_description()->updateOrCreate([
                    'user_id' => $user->id], [
                    'lead_id' => $lead->lead_id,
                    'deal_id' => $lead->deal_id,
                    'is_trainee' => 1
                ]);

                $old_invite_at = $lead->invite_at;

                $lead->invite_group_id = $request->group_id;
                $lead->day_second = $day_second;
                $lead->invite_at = $invite_at;
                $lead->user_id = $user->id;
                $lead->invited = 1;

                if ($user_type == 'remote') {
                    $msg_for_group_leader = $this->msgForGroupLeader($msg_for_group_leader, $user);
                }
                $userIds[] = $user->id;


            } else {

                $old_invite_at = $lead->invite_at;

                $lead->invite_group_id = $request->group_id;
                $lead->invite_at = $invite_at;
                $lead->day_second = $day_second;
                $lead->user_id = $user->id;
                $lead->invited = 2; // Сотрудник уже существует

                if ($user_type == 'remote') {
                    $msg_for_group_leader = $this->msgForGroupLeader($msg_for_group_leader, $user);
                }

                $user->user_description()->updateOrCreate([
                    'user_id' => $user->id], [
                    'lead_id' => $lead->lead_id,
                    'deal_id' => $lead->deal_id,
                    'is_trainee' => 1
                ]);

//                UserDescription::make([  // почему make а не create ? не ясно
//                    'user_id' => $user->id,
//                    'lead_id' => $lead->lead_id,
//                    'deal_id' => $lead->deal_id,
//                    'is_trainee' => 1,
//                ]);

                $user->segment = $lead->segment;
                $user->referrer_id = $lead->referrer_id;
                $userIds[] = $user->id;

                $user->save();
            }


            $lead->save();

            /**
             * zarplata
             */
            $zarplata = Zarplata::where('user_id', $user->id)->first();
            if ($zarplata) {
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
            /*******  Зачисление пользователя в отдел */
            /*==============================================================*/

            /* Удаление стажера из всех груп */
            GroupUser::where('user_id', $user->id)
                ->whereNull('to')
                ->update([
                    'to' => date('Y-m-d'),
                    'status' => 'drop',
                ]);

            /* Зачисление в выбранную отдел */
            GroupUser::create([
                'user_id' => $user->id,
                'group_id' => $group->id,
                'from' => date('Y-m-d'),
                'status' => 'active',
            ]);

            /*==============================================================*/
            /*******  Начало стажировки */
            /*==============================================================*/

            $date = $request->date;

            if ($old_invite_at) {
                $daytype = DayType::where([
                    'user_id' => $user->id,
                    'type' => DayType::DAY_TYPES['TRAINEE'],
                    'date' => Carbon::parse($old_invite_at)->format('Y-m-d'),
                ])->first();

                if ($daytype) $daytype->delete();
            }

            $daytype = DayType::where([
                'user_id' => $user->id,
                'date' => $date,
            ])->first();

            if ($daytype) {
                $daytype->admin_id = 1;
                $daytype->type = DayType::DAY_TYPES['TRAINEE'];
                $daytype->save();
            } else {
                $daytype = DayType::query()
                    ->create([
                        'user_id' => $user->id,
                        'type' => DayType::DAY_TYPES['TRAINEE'],
                        'email' => '',
                        'date' => $date,
                        'admin_id' => 1,
                    ]);
            }

            /*========================= Обновление webhook-ом время собеседование =====================================*/

            if ($lead->deal_id) {
                $bitrix = new Bitrix('intellect');

                if ($lead->inhouse) {
                    $bitrix->updateDeal($lead->deal_id, ["UF_CRM_1633576992" => Carbon::parse($request->date . " " . $request->time)->subHour(3)->format('Y-m-d H:i:s')]);
                } else {
                    $bitrix->updateDeal($lead->deal_id, ["UF_CRM_1568000119" => Carbon::parse($request->date . " " . $request->time)->subHour(3)->format('Y-m-d H:i:s')]);
                    $bitrix->updateDeal($lead->deal_id, ["UF_CRM_1648978687" => Carbon::parse($request->date . " " . $request->time)->subHour(3)->addDay()->format('Y-m-d H:i:s')]);
                }
            }
        }
        /*==============================================================*/
        /*******  Уведомление стажёров на whatsApp */
        /*==============================================================*/
        if (tenant('id') === 'bp') {
            $service->handle($userIds);
        }
        /*==============================================================*/
        /*******  Уведомление руководителю группы */
        /*==============================================================*/

        $msg_for_group_leader .= 'Приглашение в отдел "' . $group->name . '" на ' . date('d.m.Y', strtotime($request->date)) . ' в 9:30';

        $heads = json_decode($group->head_id);

        $timestampx = now();

        $notification_receivers = NotificationTemplate::getReceivers(6, $group->id);


        if ($has_remote_to_send_notification) {
            foreach ($notification_receivers as $user_id) {
                UserNotification::create([
                    'user_id' => $user_id,
                    'about_id' => 0,
                    'title' => 'Добавьте в Ватсап',
                    'message' => $msg_for_group_leader,
                    'group' => $timestampx
                ]);
            }
        }


        return response()->json([
            'code' => 200
        ]);
    }

    /**
     * Сформировать сообщение для руководителя на ватсап
     */
    private function msgForGroupLeader(string $msg, User $user)
    {
        $msg .= $user->phone;
        // if($user->phone_1) $msg .= ', ' . $user->phone_1;
        // if($user->phone_2) $msg .= ', ' . $user->phone_2;
        $msg .= ' : ' . $user->last_name . ' ' . $user->name . '<br>';
        return $msg;
    }

    public function update(Request $request)
    {

    }

    /**
     * Создать лид стажера вручную
     */
    public function createRecrutingLead(Request $request)
    {
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
     * Экспорт активностей (Подробная аналитика) группы в Excel
     */
    public function exportActivityExcel(Request $request)
    {

        $group = ProfileGroup::find($request->group_id);

        $request->month = (int)$request->month;
        $currentUser = auth()->user();

        $editors_id = json_decode($group->editors_id);
        $group_editors = is_array($editors_id) ? $editors_id : [];

        // Доступ к группе
        if ($currentUser->id != 18 && !in_array($currentUser->id, $group_editors)) {
            return [
                'error' => 'access',
            ];
        }

        $this->users = User::withTrashed()->whereIn('id', json_decode($group->users))
            ->get(['ID as id', 'email as email', 'name as name', 'last_name as surname', DB::raw("CONCAT(last_name,' ',name) as full_name")]);;

        /****************************** */
        /******==================================== */
        $date = Carbon::createFromDate($request->year, $request->month, 1);

        $title = 'Аналитика активностей ' . $request->month . ' месяц ' . $request->year;

        if (in_array($request->group_id, [35, 42, 56])) { // Kaspi
            $data = json_decode($this->getActivities($request), true);
            $title = 'Аналитика активностей KASPI ' . $request->month . ' месяц ' . $request->year;
        } else {
            $data = json_decode($this->getActivities($request), true);
        }

        /******==================================== */

        $sheets = [];

        $minute_headings = Activity::getHeadings($date, Activity::UNIT_MINUTES);
        $percent_headings = Activity::getHeadings($date, Activity::UNIT_PERCENTS);

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

        // @TODO Наверное не нужен
        if (tenant('id') == 'bp') {
            if ($request->group_id == 31) {

                $sheets = [
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
            } else if (in_array($request->group_id, [53])) {
                $sheets = [];
                foreach ($data as $item) {
                    $_headings = $item['plan_unit'] == 'minutes' ? $minute_headings : $percent_headings;
                    $_units = $item['plan_unit'] == 'minutes' ? Activity::UNIT_MINUTES : Activity::UNIT_PERCENTS;
                    $sheets[] = [
                        'title' => $item['name'],
                        'headings' => $_headings,
                        'sheet' => Activity::getSheet($item['records'], $date, $_units),
                    ];
                }
            }
        }

        /******==================================== */

        if (ob_get_length() > 0) ob_clean(); //  ob_end_clean();

        if ($date->daysInMonth == 28) $last_cell = 'AH3';
        if ($date->daysInMonth == 29) $last_cell = 'AI3';
        if ($date->daysInMonth == 30) $last_cell = 'AJ3';
        if ($date->daysInMonth == 31) $last_cell = 'AK3';

        Excel::create($title, function ($excel) use ($sheets, $last_cell) {
            $excel->setTitle('Отчет');
            $excel->setCreator('Laravel Media')->setCompany('MediaSend KZ');
            $excel->setDescription('экспорт данных в Excel файл');

            foreach ($sheets as $page) {
                $excel->sheet($page['title'], function ($sheet) use ($page, $last_cell) {

                    $sheet->fromArray($page['sheet'], null, 'A4', false, false);
                    $sheet->prependRow(3, $page['headings']);

                    $sheet->cell('A1', function ($cell) use ($page) {
                        $cell->setValue($page['title']);
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(14);
                    });

                    $sheet->cell('A3:' . $last_cell, function ($cell) {
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
    public function changeRecruiterProfile(Request $request)
    {
        RecruiterStat::changeProfile($request['user_id'], $request['profile'], Carbon::createFromDate($request->year, $request->month, $request->day));
    }

    public function getActiveTrainees(Request $request)
    {

        $arr = [];

        $date = Carbon::parse($request->date);
        $groups1 = ProfileGroup::whereIn('id', [42])->get();
        $groups = ProfileGroup::where('active', 1)->where('has_analytics', 1)->get();
        $groups = $groups->merge($groups1);
        $startDate = Carbon::now();
        foreach ($groups as $group) {
            $item = [];

            $item['name'] = $group->name;

            $leads = Lead::whereYear('invite_at', $date->year)
                ->whereMonth('invite_at', $date->month)
                ->where('invite_group_id', $group->id)
                ->get();

            $trainee_users = DB::table('users')
                ->whereNull('deleted_at')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->whereIn('users.id', json_decode($group->users))
                ->where('ud.is_trainee', 1)
                ->get(['users.id'])
                ->pluck('id')
                ->toArray();

            $item['sent'] = $leads->count();

            $item['working'] = 1;

            $percent = $item['sent'] > 0 ? $item['working'] / $item['sent'] * 100 : 0;
            $item['percent'] = round($percent, 1);

            $item['active'] = DayType::where('date', $date->toDateString())->whereIn('user_id', $trainee_users)->whereIn('type', [5, 7])->get()->count();//$item['sent'];
            array_push($arr, $item);
        }

        return ['ocenka_svod' => $arr];
    }


    public function getRefLinks(Request $request)
    {
        return \App\Models\BPReflink::get();
    }

    public function saveRefLinks(Request $request)
    {
        $id = $request->id;

        if ($request->method() == 'save') {
            if (strlen($request->name) == 0) return $id;
            if ($request->id == 0) {
                $item = \App\Models\BPReflink::create([
                    'name' => $request->name,
                    'info' => $request->info
                ]);

                $id = $item->id;
            } else {
                \App\Models\BPReflink::where('id', $request->id)->update([
                    'name' => $request->name,
                    'info' => $request->info
                ]);
            }

        }

        if ($request->method() == 'delete') {
            \App\Models\BPReflink::where('id', $request->id)->delete();
        }

        return $id;

    }

}

