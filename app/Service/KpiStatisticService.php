<?php

namespace App\Service;

use App\CacheStorage\KpiItemsCacheStorage;
use App\Classes\Helpers\Currency;
use App\Filters\Articles\BonusFilter;
use App\Filters\Kpis\KpiFilter;
use App\Http\Requests\BonusesFilterRequest;
use App\Models\Analytics\Activity;
use App\Models\Analytics\AnalyticStat;
use App\Models\Analytics\UpdatedUserStat;
use App\Models\Analytics\UserStat;
use App\Models\Kpi\Bonus;
use App\Models\Kpi\Kpi;
use App\Models\Kpi\KpiItem;
use App\Models\QuartalPremium;
use App\Position;
use App\ProfileGroup;
use App\Service\Department\UserService;
use App\Traits\KpiHelperTrait;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class KpiStatisticService
{
    use KpiHelperTrait;

    /**
     * Фильтры!
     *
     * Любая дата.
     */
    const ANY_DATE = 0;

    /**
     * Вчера.
     */
    const YESTERDAY = 1;

    /**
     * Сегодня.
     */
    const TODAY = 2;

    /**
     * Вчера.
     */
    const TOMORROW = 3;

    /**
     * Текущий месяц.
     */
    const CURRENT_MONTH = 4;

    /**
     * МЕСЯЦ.
     */
    const MONTH = 5;

    /**
     * Диапазон.
     */
    const RANGE = 6;

    /**
     * Модель группы.
     */
    const PROFILE_GROUP = 'App\ProfileGroup';

    /**
     * Модель пользователей.
     */
    const USER = 'App\User';

    /**
     * Модель позиций.
     */
    const POSITION = 'App\Position';

    const TARGET_TYPES = [
        0 => 'App\ProfileGroup',
        1 => 'App\User',
        2 => 'App\ProfileGroup',
        3 => 'App\Position',
    ];

    /**
     * Workdays for kpi_items
     */
    public $workdays;

    /**
     * UpdatedUserStat for kpi_items
     */
    public $updatedValues;

    /**
     * Daily plan from histories for kpi_items
     */
    public $dailyPlans;

    /**
     * Asis for recruiters
     * temp decision
     *
     * after moving from asi to userstat delete
     */
    public $asis;
    private Carbon $from;
    private Carbon $to;

    public function fetch(array $parameters): array
    {
        $groupId = $parameters['filter']['group_id'] ?? null;

        $group = ProfileGroup::query()->findOrFail($groupId);
        return $this->getUserKpis($group, $parameters);
    }

    /**
     * @param $group
     * @param $parameters
     * @return array
     */
    private function getUserKpis($group, $parameters): array
    {
        $userIds = $group->users()->pluck('id')->toArray();

        return User::query()->whereIn('id', $userIds)->whereHas('kpis')->with([
            'kpis' => function ($kpi) use ($parameters) {
                switch (isset($parameters['filter']['created_at']['variant'])) {
                    case $parameters['filter']['created_at']['variant'] == self::ANY_DATE:
                        return $kpi->whereDate('created_at', '2022-08-13');
                    case $parameters['filter']['created_at']['variant'] == self::YESTERDAY:
                        return $kpi->whereDate('created_at', Carbon::yesterday());
                    case $parameters['filter']['created_at']['variant'] == self::TODAY:
                        return $kpi->whereDate('created_at', Carbon::today());
                    case $parameters['filter']['created_at']['variant'] == self::TOMORROW:
                        return $kpi->whereDate('created_at', Carbon::tomorrow());
                    case $parameters['filter']['created_at']['variant'] == self::CURRENT_MONTH:
                        return $kpi->whereMonth('created_at', Carbon::now()->month);
                    case $parameters['filter']['created_at']['variant'] == self::MONTH:
                        return $kpi->whereMonth('created_at', '=', $parameters['filter']['created_at']['month'])
                            ->whereYear('created_at', '=', $parameters['filter']['created_at']['year']);
                    case $parameters['filter']['created_at']['variant'] == self::RANGE:
                        return $kpi->whereDate('created_at', '>', $parameters['filter']['created_at']['from'])
                            ->whereDate('created_at', '<', $parameters['filter']['created_at']['to']);
                }
            }
        ])->get()->toArray();
    }

    /**
     * С фронта прилитает тип метода подробнее в CalculateKpiService
     * @param Request $request
     * @param User $user
     * @return array
     * @throws Exception
     */
    public function get(Request $request, User $user)
    {
        $method = $request->input('method');
        $date = $request->input('date');

        return $this->calculateStatistics($user, $method, $date);
    }

    /**
     * @param User $user
     * @param $method
     * @param $date
     * @return array
     * @throws Exception
     */
    private function calculateStatistics(User $user, $method, $date): array
    {
        $statistics = $this->getUserStatistics((int)$user->id, $date);
        $calculateKpi = new CalculateKpiService();

        foreach ($statistics as $index => $statistic) {
            $statistics[$index]['percent'] = $calculateKpi->getCompletePercent($statistic, $method);
            $statistics[$index]['premiumSum'] = $this->sumOfActivity(
                $statistic['lower_limit'],
                $statistic['upper_limit'],
                $calculateKpi->getCompletePercent($statistic, $method),
                $statistic['share'],
                $statistic['completed_80'],
                $statistic['completed_100']
            );
        }

        return $statistics;
    }

    /**
     * Получаем статистику для пользователя и kpi и kpi_items для пользователя
     * и получаем все нужные данные
     * @value kpi_id
     * @value activity_id
     * @value daily_pan
     * @value value
     * @value workdays
     * @value is_user_full_time
     * @param int $userId
     * @param $date
     * @return array
     */
    private function getUserStatistics(int $userId, $date): array
    {
        $userStats = User::query()
            ->join('user_stats', 'users.id', '=', 'user_stats.user_id')
            ->where('users.id', $userId)
            ->groupByRaw('MONTH(user_stats.created_at), user_stats.user_id, user_stats.activity_id, users.full_time, users.working_day_id')
            ->select(
                DB::raw('sum(user_stats.value) as total_fact'),
                'user_stats.user_id',
                'user_stats.activity_id',
                'users.full_time',
                'users.working_day_id'
            )->get()->toArray();

        $kpItems = Kpi::query()
            ->join('kpi_items', 'kpis.id', '=', 'kpi_items.kpi_id')
            ->where([
                ['targetable_id', '=', $userId],
                ['targetable_type', '=', 'App\User']
            ])->get()->toArray();

        $statistics = [];

        foreach ($userStats as $userStat) {
            foreach ($kpItems as $userPlan) {
                if ($userStat['activity_id'] == $userPlan['activity_id']) {
                    $workdays = $userStat['working_day_id'] != 1 ? [6, 0] : [0];
                    $statistics[] = [
                        'kpi_id' => $userPlan['kpi_id'],
                        'activity_id' => $userStat['activity_id'],
                        'daily_plan' => $userPlan['plan'],
                        'total_fact' => $userStat['total_fact'],
                        'is_user_full_time' => $userStat['full_time'],
                        'workdays' => workdays(date('Y'), date('m'), $workdays),
                        'days_from_user_applied' => 0,
                        'records_count' => $this->getRecordsCount($date, $userId),
                        'lower_limit' => $userPlan['lower_limit'],
                        'upper_limit' => $userPlan['upper_limit'],
                        'share' => $userPlan['share'],
                        'completed_80' => $userPlan['completed_80'],
                        'completed_100' => $userPlan['completed_100']
                    ];
                }
            }
        }

        return $statistics;
    }

    /**
     *  кол-во записей с UserStat
     */
    private function getRecordsCount(array $date, int $userId): int
    {
        return UserStat::query()->where('user_id', $userId)->when(!empty($date), function ($kpi) use ($date) {
            $kpi->whereYear('created_at', $date['year'])->whereMonth('created_at', $date['month']);
        })->count();
    }

    /**
     * Расчет суммы К выдаче по результатам KPI
     */
    private function sumOfActivity(
        int   $lower_limit,
        int   $upper_limit,
        float $completed_percent,
        int   $share,
        float $completed_80,
        float $completed_100
    ): float|int
    {
        $result = 0;
        $completed_percent = 80;
        $lower_limit = $lower_limit / 100;
        $upper_limit = $upper_limit / 100;
        $completed_percent = $completed_percent / 100;
        $share = $share / 100;

        if ($completed_percent > $lower_limit) {
            if ($completed_percent < $upper_limit) {
                $result = $completed_80 * $share * ($completed_percent - $lower_limit) * $upper_limit / ($upper_limit - $lower_limit);
            } else {
                $result = $completed_100 * $share * $completed_percent;
            }
        } else {
            $result = 0;
        }


        if ($result < 0) {
            $result = 0;
        }
        return $result;
    }

    /**
     * Получаем KPI бонусы.
     * @param BonusesFilterRequest $request
     * @return array
     */
    public function fetchBonuses(BonusesFilterRequest $request, BonusFilter $filter): array
    {
        $bonuses = Bonus::filter($filter)->get();

        $kpiBonuses = [];

        foreach ($bonuses as $bonus) {
            if ($bonus->targetable_type == self::PROFILE_GROUP) {
                $kpiBonuses[] = $this->getProfileGroupBonus($bonus, $request);
            }

            if ($bonus->targetable_type == self::USER) {
                $kpiBonuses[] = $this->getUserBonus($bonus, $request);
            }

            if ($bonus->targetable_type == self::POSITION) {
                $kpiBonuses[] = $this->getPositionBonus($bonus, $request);
            }
        }

        return $kpiBonuses;
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    private function getBonuses(Request $request)
    {
        $parameters = $request->all();
        $type = isset($parameters['targetable_type']) ? $this->getModel($parameters['targetable_type']) : null;
        $id = $parameters['targetable_id'] ?? null;

        return Bonus::query()
            ->when(isset($type) && isset($id), fn($kpi) => $kpi->where([
                ['targetable_type', $type],
                ['targetable_id', $id]
            ]))
            ->get();
    }

    /**
     * Получаем по Отделам.
     * @param $bonus
     * @param $request
     */
    private function getProfileGroupBonus($bonus, $request)
    {
        $userId = $request->user_id ?? null;
        $month = $request->month ?? null;
        $year = $request->year ?? null;

        $group = ProfileGroup::with([
            'bonuses' => fn($bs) => $bs->where('activity_id', $bonus->activity_id)
                ->when($year && $month, fn($bns) => $bns->whereYear('created_at', $year)->whereMonth('created_at', $month)),
            'users' => fn($user) => $user->select('id', 'name', 'last_name'),
            'users.obtainedBonuses' => fn($obtainedBns) => $obtainedBns->where('bonus_id', $bonus->id),
        ])
            ->where('id', $bonus->targetable_id)
            ->select(['id', 'name'])->first();

        if ($group) {
            $group->targetable_type = $bonus->targetable_type;
            $group->targetable_id = $bonus->targetable_id;
            $group->activity_id = $bonus->activity_id;
            $group->bonus_id = $bonus->id;
        }

        return $group;
    }

    /**
     * Получаем сотрудников.
     */
    private function getUserBonus($bonus, $request)
    {
        $month = $request->month ?? null;
        $year = $request->year ?? null;

        $user = User::with([
            'bonuses' => function ($bs) use ($bonus, $month, $year) {
                $bs
                    ->when($year && $month, fn($bonus) => $bonus->whereYear('created_at', $year)->whereMonth('created_at', $month))
                    ->where('activity_id', $bonus->activity_id);
            },
            'bonuses.obtainedBonuses'
        ])
            ->where('id', $bonus->targetable_id)
            ->select(['id', 'name', 'last_name'])
            ->first();

        if ($user) {
            $user->targetable_type = $bonus->targetable_type;
            $user->targetable_id = $bonus->targetable_id;
            $user->activity_id = $bonus->activity_id;
            $user->bonus_id = $bonus->id;
        }

        return $user;
    }

    /**
     * Получаем по позициям.
     * @param $bonus
     * @param $request
     */
    private function getPositionBonus($bonus, $request)
    {
        $userId = $request->user_id ?? null;
        $month = $request->month ?? null;
        $year = $request->year ?? null;

        $position = Position::with([
            'bonuses' => fn($bs) => $bs
                ->where('activity_id', $bonus->activity_id)
                ->when($year && $month, fn($bonus) => $bonus->whereYear('created_at', $year)->whereMonth('created_at', $month)),
            'users' => fn($user) => $user->select('id', 'position_id', 'name', 'last_name'),
            'users.obtainedBonuses' => fn($obtainedBns) => $obtainedBns->where('bonus_id', $bonus->id),
        ])
            ->where('id', $bonus->targetable_id)->select(['id', 'position'])->first();

        if ($position) {
            $position->targetable_type = $bonus->targetable_type;
            $position->targetable_id = $bonus->targetable_id;
            $position->activity_id = $bonus->activity_id;
            $position->bonus_id = $bonus->id;
        }

        return $position;
    }

    /**
     * Список Квартальных премии
     */
    public function fetchQuartalPremiums(Request $request): array
    {

        $all = $request->all();
        $userId = $all['filters']['user_id'] ?? null;

        $quartalPremiums = $this->getQuartalPremiums($request);
        $users = [];
        $profileGroups = [];
        $positions = [];
        $authUser = $userId ? User::getUserById($userId) : null;

        $groups = ProfileGroup::query()
            ->select([
                DB::raw('profile_groups.*'),
                DB::raw('group_user.status as status'),
            ])
            ->join('group_user', 'group_user.group_id', 'profile_groups.id')
            ->distinct()
            ->where('status', 'active')->get();

        $read = $quartalPremiums->contains(fn($q) => in_array($userId, $q->read_by ?? []));

        foreach ($quartalPremiums as $quartalPremium) {
            if ($quartalPremium->targetable_type == self::USER) {
                $user = $this->getUsersQp($quartalPremium);

                if (empty($user)) {
                    continue;
                }

                $users[] = [
                    'targetable_id' => $quartalPremium->targetable_id,
                    'targetable_type' => $quartalPremium->targetable_type,
                    'id' => $user->user_id,
                    'name' => $user->full_name,
                    'items' => [
                        'activity_id' => $user->activity_id,
                        'activity' => $quartalPremium->activity,
                        'title' => $quartalPremium->title,
                        'text' => $quartalPremium->text,
                        'plan' => $quartalPremium->plan,
                        'from' => $quartalPremium->from,
                        'to' => $quartalPremium->to,
                        'sum' => $quartalPremium->sum,
                        'method' => $quartalPremium->method,
                        'fact' => $user->fact
                    ]
                ];
            }

            if ($quartalPremium->targetable_type == self::PROFILE_GROUP) {
                $query = $authUser ? $authUser->groups()->where('status', 'active')->get() : $groups;
                $groupIds = $query->pluck('id')->toArray() ?? [];

                if (empty($groupIds)) {
                    continue;
                }

                $profileGroups[] = in_array($quartalPremium->targetable_id, $groupIds) ? $quartalPremium : null;
            }

            if ($quartalPremium->targetable_type == self::POSITION) {
                $positionId = $authUser?->position->id ?? null;

                if ($positionId == null) {
                    continue;
                }

                $positions[] = $quartalPremium->targetable_id == $positionId ? $quartalPremium : null;
            }
        }

        return [
            'data' => [
                $users,
                $profileGroups,
                $positions
            ],
            'read' => $read,
        ];
    }

    /**
     * Получаем кв-премий.
     */
    private function getQuartalPremiums(Request $request)
    {
        $type = isset($request->targetable_type) ? $this->getModel($request->targetable_type) : null;
        $id = $request->targetable_id ?? null;

        /**
         * eeeee
         */

        $filters = $request->filters;

        /**
         * filters
         *
         * date_from
         * user_id
         */
        if (
            isset($filters['data_from']['year'])
            && isset($filters['data_from']['month'])
        ) {
            $date = Carbon::createFromDate(
                $filters['data_from']['year'],
                $filters['data_from']['month'],
                1
            );
        } else {
            $date = Carbon::now()->setTimezone('Asia/Almaty')->startOfMonth();
        }

        $user_id = isset($filters['user_id']) ? $filters['user_id'] : 0;

        /**
         * indiv or common
         */
        if ($user_id != 0) {
            $qps = QuartalPremium::withoutTrashed();

            $user = User::withTrashed()->with('groups')->find($user_id);
            $position_id = $user->position_id;

            $groups = $user->groups->pluck('id')->toArray();

            $qps->where(function ($query) use ($user_id, $groups, $position_id) {
                $query->where(function ($q) use ($user_id) {
                    $q->where('targetable_id', $user_id)
                        ->where('targetable_type', 'App\User');
                })
                    ->orWhere(function ($q) use ($groups) {
                        $q->whereIn('targetable_id', $groups)
                            ->where('targetable_type', 'App\ProfileGroup');
                    })
                    ->orWhere(function ($q) use ($position_id) {
                        $q->where('targetable_id', $position_id)
                            ->where('targetable_type', 'App\Position');
                    });
            });

        } else {
            $qps = QuartalPremium::query()->when(isset($type) && isset($id), fn($qp) => $qp->where([
                ['targetable_type', $type],
                ['targetable_id', $id]
            ]));
        }

        $qps = $qps->with('activity')
            ->whereDate('from', '<=', $date->format('Y-m-d'))
            ->whereDate('to', '>=', $date->format('Y-m-d'))
            // ->whereDate('created_at', '<=', Carbon::parse($date->format('Y-m-d'))
            //                                         ->endOfMonth()
            //                                         ->format('Y-m-d')
            // )
            ->get();

        return $qps;
    }

    /**
     * Получаем кв-премий индивидуальные.
     */
    private function getUsersQp($quartalPremium)
    {
        return User::query()->leftJoin('user_stats as us', 'us.user_id', '=', 'users.id')
            ->select(['users.id', 'user_id', 'activity_id', 'name', DB::raw('SUM(value) as fact'), DB::raw("CONCAT_WS(' ', users.last_name, users.name) as full_name")])
            //    ->where([
            //        ['activity_id', $quartalPremium->activity_id]
            //    ])
            ->where('users.id', $quartalPremium->targetable_id)
            // ->whereBetween('us.date', [$quartalPremium->from, $quartalPremium->to])
            ->groupBy('activity_id', 'user_id', 'full_name')
            ->first();
    }

    /**
     * Вытащить список kpi со статистикой
     *
     * getUsersForKpi($kpi)
     * getUserStats($kpi, $_user_ids, $date)
     * connectKpiWithUserStats(Kpi $kpi, $_users)
     */
    public
    function fetchKpis(Request $request): array
    {
        $filters = $request->filters;

        /**
         * filters
         *
         * date_from
         * user_id
         */
        if (
            isset($filters['data_from']['year'])
            && isset($filters['data_from']['month'])
        ) {
            $date = Carbon::createFromDate(
                $filters['data_from']['year'],
                $filters['data_from']['month'],
                1
            );
        } else {
            $date = Carbon::now()->setTimezone('Asia/Almaty')->startOfMonth();
        }

        $user_id = isset($filters['user_id']) ? $filters['user_id'] : 0;

        $this->workdays = collect($this->userWorkdays($request->get('filters')));
        $this->updatedValues = UpdatedUserStat::query()
            ->whereMonth('date', $date->month)
            ->whereYear('date', $date->year)
            ->orderBy('date', 'desc')
            ->get();
        /**
         * get kpis
         */
        $last_date = Carbon::parse($date)->endOfMonth()->format('Y-m-d');

        $kpis = Kpi::with([
            'histories_latest' => function ($query) use ($last_date) {
                $query->whereDate('created_at', '<=', $last_date);
            },
            'items.histories_latest' => function ($query) use ($last_date) {
                $query->whereDate('created_at', '<=', $last_date);
            },
            'items' => function ($query) use ($last_date) {
                $query->whereDate('created_at', '<=', $last_date);
            },
            'items.activity'
        ]);

        $droppedGroups = array();
        if ($user_id != 0) {
            /** @var User $user */
            $user = User::withTrashed()->with('groups')->find($user_id);
            $position_id = $user->position_id;

            $groups = ($user->inGroups())->pluck('id')->toArray();
            $droppedGroups = $user->droppedGroups($date);

            $groups = array_merge($groups, $droppedGroups);

            $kpis->where(function ($query) use ($user_id, $groups, $position_id) {
                $query->where(function ($q) use ($user_id) {
                    $q->where('targetable_id', $user_id)
                        ->where('targetable_type', 'App\User');
                })
                    ->orWhere(function ($q) use ($groups) {
                        $q->whereIn('targetable_id', $groups)
                            ->where('targetable_type', 'App\ProfileGroup');
                    })
                    ->orWhere(function ($q) use ($position_id) {
                        $q->where('targetable_id', $position_id)
                            ->where('targetable_type', 'App\Position');
                    });
            });
        }

        $kpis = $kpis
            ->whereDate('created_at', '<=', $last_date)
            ->where(fn($query) => $query->whereNull('deleted_at')->orWhereDate('deleted_at', '>', $last_date))
            ->where('is_active', true)
            ->whereNot(function (Builder $query) use ($last_date, $date) {
                $query->where('targetable_type', 'App\\User')
                    ->whereHas('user', function (Builder $query) use ($last_date, $date) {
                        $query->where('deleted_at', '<', $last_date);
                    });
            })
            ->orderBy('targetable_type', 'desc')
            ->limit(3)
            ->get();

        $read = $kpis->contains(fn($k) => in_array($user_id, $k->read_by ?? []));

        foreach ($kpis as $kpi) {
            $kpi->kpi_items = [];

            // remove items if it's not in history
            if ($kpi->histories_latest) {
                $payload = json_decode($kpi->histories_latest->payload, true);

                if (isset($payload['children'])) {
                    $kpi->items = $kpi->items->whereIn('id', $payload['children']);
                }
                $kpi->completed_80 = $payload['completed_80'];
                $kpi->completed_100 = $payload['completed_100'];
            }

            $kpi->users = $this->getUsersForKpi($kpi, $date, $user_id);
            $kpi_sum = 0;
            foreach ($kpi->users as $user) {
                $kpi_sum = $kpi_sum + $user['avg_percent'];
            }

            $kpi->avg = count($kpi->users) > 0 ? round($kpi_sum / count($kpi->users), 2) : 0; //AVG percent of all KPI of all USERS in GROUP

            $kpi['dropped'] = in_array($kpi->targetable_id, $droppedGroups) ?? true;
        }

        return [
            'items' => $kpis,
            'activities' => Activity::get(),
            'groups' => ProfileGroup::get()->pluck('name', 'id')->toArray(),
            'user_id' => auth()->user() ? auth()->id() : 1,
            'read' => $read,
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function userWorkdays(?array $filter = null): array
    {
        $default_date = ['year' => Carbon::now()->year, 'month' => Carbon::now()->month];
        $filters = $filter ?? ['data_from' => $default_date];
        if (!array_key_exists('data_from', $filters)) $filters['data_from'] = $default_date;

        $users = $this->getUserProfileGroup($filters);

        $result = [];

        foreach ($users as $user) {
            if ($user->applied == null) {
                continue;
            }

            $userAppliedDate = Carbon::createFromFormat('Y-m-d H:i:s', $user->applied);

            if ($userAppliedDate->year > $filters['data_from']['year']) {
                continue;
            }

            $ignore = $user->working_day_id == 1 ? [6, 0] : [0];
            $userWorkDays = $this->workdays($userAppliedDate->year, $userAppliedDate->month, $userAppliedDate->day, $ignore);
            $workdaysInMonth = workdays($filters['data_from']['year'], $filters['data_from']['month'], $ignore);

            if ($userAppliedDate->year == $filters['data_from']['year'] && $userAppliedDate->month == $filters['data_from']['month']) {
                $result[] = [
                    'user_id' => $user->user_id,
                    'activity_id' => $user->activity_id,
                    'applied_at' => $user->applied,
                    'user_work_days' => $userWorkDays,
                    'workdays_in_month' => $workdaysInMonth,
                    'user_plan' => $user->full_time == 1 ? $userWorkDays * $user->plan : $userWorkDays * $user->plan / 2,
                    'workdays' => $user->working_day_id == 1 ? 5 : 6,
                    'weekdays' => $user->weekdays
                ];
            } else {
                $result[] = [
                    'user_id' => $user->user_id,
                    'activity_id' => $user->activity_id,
                    'applied_at' => $user->applied,
                    'user_work_days' => $workdaysInMonth,
                    'workdays_in_month' => $workdaysInMonth,
                    'user_plan' => $user->full_time == 1 ? $workdaysInMonth * $user->plan : $workdaysInMonth * $user->plan / 2,
                    'workdays' => $user->working_day_id == 1 ? 5 : 6,
                    'weekdays' => $user->weekdays
                ];
            }
        }

        return $result;
    }

    /**
     * @param $filters
     * @return Collection|array
     */
    private
    function getUserProfileGroup($filters): Collection|array
    {
        return User::query()
            ->join('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->join('group_user as gu', 'gu.user_id', '=', 'users.id')
            ->join('kpis as kp', fn($kp) => $kp->on('kp.targetable_id', '=', 'gu.group_id')->where('kp.targetable_type', '=', self::PROFILE_GROUP))
            ->join('kpi_items as ki', 'ki.kpi_id', '=', 'kp.id')
            ->join('activities as a', 'ki.activity_id', '=', 'a.id')
            ->where('ud.is_trainee', 0)
            ->get();
    }

    /**
     * @param $year
     * @param $month
     * @param $day
     * @param array $ignore
     * @return int
     */
    private
    function workdays($year, $month, $day, array $ignore = [6, 0]): int
    {
        $count = 0;
        $counter = mktime(0, 0, 0, $month, $day, $year);
        while (date("n", $counter) == $month) {
            if (!in_array(date("w", $counter), $ignore)) {
                $count++;
            }
            $counter = strtotime("+1 day", $counter);
        }
        return $count;
    }

    /**
     * Helper for fetchKpis()
     */
    public function getUsersForKpi(
        Kpi    $kpi,
        Carbon $date,
        int    $user_id = 0
    ): array
    {
        $dateFrom = $date->copy()->startOfMonth();
        $dateTo = $date->copy()->endOfMonth();


        $type = $kpi->target['type'] ?? 0;
        // User::class
        if ($type == 1) {
            $_user_ids = [$kpi->targetable_id];
        }

        // ProfileGroup::class
        if ($type == 2) {
            $profileGroup = ProfileGroup::query()->findOrFail($kpi->targetable_id);
            $_user_ids = (new UserService)->getEmployeesWithFired($profileGroup->id, $date)
                ->pluck('id')
                ->toArray();
            if ($user_id != 0) $_user_ids = [$user_id];
        }

        // Position::class
        if ($type == 3) {
            $_user_ids = User::withTrashed()
                ->where(function (Builder $query) use ($dateTo) {
                    $query->whereNull('deleted_at');
                    $query->orWhere('deleted_at', '>=', $dateTo->format("Y-m-d"));
                })
                ->with(['profile_histories_latest' => function ($query) use ($dateFrom, $dateTo) {
                    $query->whereBetween('created_at', [$dateFrom->format("Y-m-d"), $dateTo->format("Y-m-d")]);
                }])
                ->get()
                ->filter(function (User $user) use ($kpi) {
                    $history = $user->profile_histories_latest;
                    if ($history) {
                        $positionsId = json_decode($history->payload, true)['position_id'];
                        return $positionsId == $kpi->targetable_id;
                    }
                    return $user->position_id == $kpi->targetable_id;
                })
                ->pluck('id')
                ->toArray();
        }


        if ($type == 0) {
            $_user_ids = [];
            $piv_users = $kpi->users()
                ->select('kpiable_id')
                ->pluck('kpiable_id')
                ->toArray();
            $_user_ids = array_merge($piv_users, $_user_ids);

            $piv_positions = $kpi->positions()
                ->select('kpiable_id')
                ->pluck('kpiable_id')
                ->toArray();

            $_user_ids = array_merge($_user_ids, User::withTrashed()
                ->where(function (Builder $query) use ($dateTo) {
                    $query->whereNull('deleted_at');
                    $query->orWhere('deleted_at', '>=', $dateTo->format("Y-m-d"));
                })
                ->with(['profile_histories_latest' => function ($query) use ($dateFrom, $dateTo) {
                    $query->whereBetween('created_at', [$dateFrom->format("Y-m-d"), $dateTo->format("Y-m-d")]);
                }])
                ->get()
                ->filter(function (User $user) use ($piv_positions) {
                    $history = $user->profile_histories_latest;
                    if ($history) {
                        $positionsId = json_decode($history->payload, true)['position_id'];
                        return in_array($positionsId, $piv_positions);
                    }
                    return in_array($user->position_id, $piv_positions);
                })
                ->pluck('id')
                ->toArray(), $piv_users);

            $piv_groups = $kpi->groups()
                ->select('kpiable_id')
                ->pluck('kpiable_id')
                ->toArray();

            $_user_ids = array_merge($_user_ids,
                (new UserService())->getEmployeesWithFiredByGroupIds($piv_groups, $date)->pluck('id')
                    ->toArray()
            );
        }

        // get users with user stats
        $_users = $this->getUserStats($kpi, $_user_ids, $date);

        // create final users array
        return $this->connectKpiWithUserStats(
            $kpi,
            $_users,
            $date,
        );
    }

    /**
     * get users with user stats
     */
    private function getUserStats(Kpi $kpi, array $user_ids, Carbon $date): \Illuminate\Support\Collection
    {
        $activities = $kpi->items
            ->where('activity_id', '!=', 0)
            ->pluck('activity_id')
            ->unique()
            ->toArray();

        // subquery
        $sum_and_counts = \DB::table('user_stats')
            ->selectRaw("user_id,
				SUM(value) as fact,
				AVG(value) as avg,
				COUNT(value) as records_count,
				activity_id,
				date")
            ->whereMonth('date', $date->month)
            ->whereYear('date', $date->year)
            ->where('value', '>', 0)
            ->whereIn('user_id', $user_ids)
            ->whereIn('activity_id', $activities)
            ->groupBy('user_id', 'activity_id');

        // query
        $users = User::withTrashed()
            ->select([
                'users.id',
                'users.last_name',
                'users.name',
                'users.full_time',
                'sum_and_counts.fact',
                'sum_and_counts.avg',
                'sum_and_counts.records_count',
                'sum_and_counts.activity_id',
                'ud.applied',
                \DB::raw('datediff(CURDATE(), ud.applied) as days'),
                \DB::raw('datediff(CURDATE(), users.created_at) as registered_days')
            ])
            ->leftJoinSub($sum_and_counts, 'sum_and_counts', function ($join) {
                $join->on('users.id', '=', 'sum_and_counts.user_id');
            })
            ->join('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where('ud.is_trainee', 0)
            ->whereIn('users.id', $user_ids)
            ->orderBy('last_name')
            ->get();

        // group collection
        $users = $users->groupBy('id')
            ->map(function ($items) {
                return [
                    'id' => $items[0]->id,
                    'name' => $items[0]->last_name . ' ' . $items[0]->name,
                    'expanded' => false,
                    'full_time' => $items[0]->full_time,
                    'items' => $items->map(function ($item) {
                        $item->percent = 0;
                        $item->share = 0;
                        return $item;
                    }),
                ];
            });
        return $users->values();
    }

    /**
     * Create final users array
     *
     * connect user activity facts and avg values with kpi_items
     *
     * find fact
     * identify actual plan
     */
    private function connectKpiWithUserStats(
        Kpi    $kpi,
        mixed  $_users,
        Carbon $date,
    ): array
    {
        // count workdays in month
        $workdays = [];
        $workdays[5] = workdays($date->year, $date->month, [6, 0]);
        $workdays[6] = workdays($date->year, $date->month, [0]);

        // fill users array
        $users = [];

        /**
         * connect user activity facts and avg values with kpi_items
         */
        foreach ($_users as $user) {
            $kpi_items = [];
            $sumKpiPercent = 0;

            foreach ($kpi->items as $_item) {

                // to array because object changes every loop
                $item = $_item->toArray();

                // get last History
                if ($_item->histories_latest) {
                    $last_history = json_decode($_item->histories_latest->payload, true);
                    if (Arr::exists($last_history, 'activity_id')) $item['activity_id'] = $last_history['activity_id'];
                    if (Arr::exists($last_history, 'method')) $item['method'] = $last_history['method'];
                    if (Arr::exists($last_history, 'share')) $item['share'] = $last_history['share'];
                    if (Arr::exists($last_history, 'unit')) $item['unit'] = $last_history['unit'];
                    if (Arr::exists($last_history, 'plan')) $item['plan'] = $last_history['plan'];
                    if (Arr::exists($last_history, 'name')) $item['name'] = $last_history['name'];
                }


                // check user stat exists
                $exists = collect($user['items'])
                    ->where('activity_id', $item['activity_id'])
                    ->first();

                // assign keys
                if ($exists) {
                    $item['fact'] = $exists->fact;
                    $item['avg'] = $exists->avg;
                    $item['records_count'] = $exists->records_count;
                    $item['days'] = $exists->days;
                    $item['registered'] = $exists->registered_days;
                    $item['applied'] = $exists->applied;

                    if ($_item->activity
                        && $_item->activity->view == Activity::VIEW_QUALITY) {

                        $query = UserStat::query()
                            ->selectRaw("
                                value,
                                activity_id,
                                user_id,
                                DAY(date) as day
                            ")
                            ->whereMonth('date', $date->month)
                            ->whereYear('date', $date->year)
                            ->where('activity_id', $_item->activity_id)
                            ->where('user_id', $user['id'])
                            ->get();

                        /**
                         * if avg methods
                         * take weeks
                         *
                         */
                        if (in_array($_item->method, [2, 4, 6])) {
                            $weeks = $this->weeksArray($date->month, $date->year);

                            /**
                             * count avg of every user
                             */

                            $avg = 0;
                            $count = 0;

                            foreach ($weeks as $key => $week) {
                                $val = isset($week[0])
                                    ? $query->whereBetween('day', [$week[0], $week[count($week) - 1]])->avg('value')
                                    : 0;

                                if ($val && $val > 0) {
                                    $avg += $val;
                                    $count++;
                                }
                            }

                            $item['fact'] = $query->sum('value');
                            $item['avg'] = $count > 0 ? round($avg / $count, 2) : 0;
                            $item['records_count'] = $count;

                        } else {
                            $item['fact'] = $query->sum('value');
                            $item['avg'] = $query->avg('avg');
                            $item['records_count'] = $query->count();
                        }

                    }

                } else {
                    $item['fact'] = 0;
                    $item['avg'] = 0;
                    $item['records_count'] = 0;
                    $item['days'] = 0;
                    $item['registered'] = 0;
                    $item['applied'] = null;
                }

                /**
                 * take another activity values
                 */
                $item['fact'] = $item['fact'] ?? 0;

                $this->takeCommonValue($_item, $date, $item);
                $this->takeCellValue($_item, $date, $item);
                $this->takeRentability($_item, $date, $item);
                $this->takeUpdatedValue($_item->id,
                    $item['activity_id'],
                    $date,
                    $item,
                    $user['id']
                );

                $item = $this->calculatePercent($item);

                $sumKpiPercent = $sumKpiPercent + $item['percent'];

                // plan
                $item['full_time'] = $user['full_time'];
                $history = $_item->histories_latest;
                $has_edited_plan = $history ? json_decode($history->payload, true) : false;

                $item['daily_plan'] = (float)$_item->plan;

                if ($has_edited_plan) {
                    if (array_key_exists('plan', $has_edited_plan)) $item['daily_plan'] = $has_edited_plan['plan'];
                    if (array_key_exists('name', $has_edited_plan)) $item['name'] = $has_edited_plan['name'];
                    if (array_key_exists('share', $has_edited_plan)) $item['share'] = $has_edited_plan['share'];
                    if (array_key_exists('method', $has_edited_plan)) $item['method'] = $has_edited_plan['method'];
                    if (array_key_exists('unit', $has_edited_plan)) $item['unit'] = $has_edited_plan['unit'];
                    if (array_key_exists('cell', $has_edited_plan)) $item['cell'] = $has_edited_plan['cell'];
                    if (array_key_exists('common', $has_edited_plan)) $item['common'] = $has_edited_plan['common'];
                    if (array_key_exists('activity_id', $has_edited_plan)) $item['activity_id'] = $has_edited_plan['activity_id'];

                }

                /**
                 * If the user works part-time, the daily plan needs to be divided by 2.
                 * In the statistics, if the entire plan is 208 and the actual completed work is 104,
                 * we calculate the completion percentage as 50% for full-time.
                 * For part-time, in similar cases, we adjust the plan by dividing it by 2 (208/2=104),
                 * making the actual completed work 100%, resulting in a 100% completed KPI.
                 */
                if (!$user['full_time'] && !Str::contains('%', $item['unit'])) $item['daily_plan'] = $item['daily_plan'] / 2;
                $item['plan'] = $item['daily_plan'];

//                dd_if($user['id'] == 28606 && $item['id'] == 304, $item['plan'], $item['daily_plan']);

                /**
                 * count workdays
                 */
                $item['workdays'] = $workdays[6];
                $percent_of_plan_for_sum_method = 1;

                if ($_item->activity) {
                    $has_workdays = $this->workdays->where('user_id', $user['id'])
                        ->where('activity_id', $_item->activity->id)
                        ->first();
                    if ($has_workdays) {
                        $percent_of_plan_for_sum_method = $has_workdays['workdays_in_month'] > 0
                            ? $has_workdays['user_work_days'] / $has_workdays['workdays_in_month']
                            : 1;
                        $item['workdays'] = $has_workdays['user_work_days'];
                    }
                }
                $kpi_items[] = $item;
            }

            $user['items'] = $kpi_items;
            if (count($kpi_items) > 0) {
                $user['avg_percent'] = round($sumKpiPercent / count($kpi_items), 2);
            } else {
                $user['avg_percent'] = 0;
            }

            $users[] = $user;
        }


        return $users;
    }

    /**
     * Create weeks array with days
     * copy of method in QualityController
     */
    private function weeksArray($month, $year)
    {
        $weeks = [];
        $week_number = 1;
        $week = [];
        $daysInMonth = Carbon::createFromFormat('m-Y', $month . '-' . $year)->daysInMonth;

        for ($d = 1; $d <= $daysInMonth; $d++) {

            array_push($week, (int)$d);

            if (Carbon::createFromFormat('d-m-Y', $d . '-' . $month . '-' . $year)->dayOfWeek == Carbon::SUNDAY) {
                $weeks[$week_number] = $week;
                $week = [];
                $week_number++;
            }

            if ($d == $daysInMonth) {
                $weeks[$week_number] = $week;
            }
        }

        return $weeks;
    }

    private function takeCommonValue(KpiItem $kpi_item, Carbon $date, array &$item): void
    {
        $activity = null;
        if ($kpi_item->activity_id) $activity = Activity::query()->find($kpi_item->activity_id);
        /**
         * take quality value
         * avg goes with weeks
         */
        if ($kpi_item->common == 1 && $activity && $activity->view == Activity::VIEW_QUALITY) {

            $query = UserStat::query()
                ->selectRaw("
                    value,
                    activity_id,
                    user_id,
                    DAY(date) as day
                ")
                ->whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->where('activity_id', $kpi_item->activity_id)
                ->get();

            /**
             * if avg methods
             * take weeks
             *
             */
            if (in_array($kpi_item->method, [2, 4, 6])) {
                $weeks = $this->weeksArray($date->month, $date->year);

                $total_avg = 0;
                $total_count = 0;

                $users = $query->groupBy('user_id');

                /**
                 * count avg of every user
                 */

                //  dd(array_keys($users));
                foreach ($users as $id => $user) {

                    $avg = 0;
                    $count = 0;

                    foreach ($weeks as $key => $week) {
                        $val = isset($week[0])
                            ? $user->whereBetween('day', [$week[0], $week[count($week) - 1]])->avg('value')
                            : 0;

                        if ($val && $val > 0) {
                            $avg += $val;
                            $count++;
                        }
                    }
                    if ($count > 0) {


                        $total_avg += $count > 0 ? round($avg / $count, 2) : 0;
                        $total_count++;
                    }
                }

                $item['fact'] = $query->sum('value');
                $item['avg'] = $total_count > 0 ? round($total_avg / $total_count, 2) : 0;
                $item['records_count'] = $total_count;

            } else {
                $item['fact'] = $query->sum('value');
                $item['avg'] = $query->avg('avg');
                $item['records_count'] = $query->count();
            }

        }

        if ($kpi_item->common == 1 && $activity && $activity->view != Activity::VIEW_QUALITY) {

            if (in_array($kpi_item->method, [2, 4, 6])) {

                $query = UserStat::selectRaw("
                        SUM(value) as fact,
                        AVG(value) as avg,
                        COUNT(value) as records_count,
                        activity_id,
                        user_id,
                        date
                    ")
                    ->whereMonth('date', $date->month)
                    ->whereYear('date', $date->year)
                    ->where('value', '>', 0)
                    ->where('activity_id', $kpi_item->activity_id)
                    ->groupBy('user_id')
                    ->get();


                $item['fact'] = $query->sum('fact');
                $item['avg'] = $query->where('avg', '>', 0)->avg('avg');
                $item['records_count'] = $query->count();

            } else {

                $query = UserStat::query()
                    ->selectRaw("
                        SUM(value) as fact,
                        AVG(value) as avg,
                        COUNT(value) as records_count,
                        activity_id
                    ")
                    ->whereYear('date', $date->year)
                    ->whereMonth('date', $date->month)
                    ->where('value', '>', 0)
                    ->where('activity_id', $kpi_item->activity_id)
                    ->groupBy('activity_id')
                    ->first();

                if ($query) {
                    $item['fact'] = $query->fact;
                    $item['avg'] = $query->avg;
                    $item['records_count'] = $query->records_count;
                }
            }

        }

    }

    /**
     * take cell value from analytics
     * for kpi item
     *
     * @param $kpi_item
     * @param Carbon $date
     * @param array &$item
     *
     * @return float
     */
    private function takeCellValue(KpiItem $kpi_item, Carbon $date, array &$item): void
    {
        $activity = null;
        if ($kpi_item->activity_id) $activity = Activity::query()->find($kpi_item->activity_id);

        if ($activity && $activity->view == Activity::VIEW_CELL) {

            $item['fact'] = AnalyticStat::getCellValue(
                $activity->group_id,
                $kpi_item->cell,
                $date->firstOfMonth()->format('Y-m-d'),
                2
            );

            $item['avg'] = $item['fact'];
        }


    }

    /**
     * take rentability value from analytics
     * for kpi item
     *
     * @param KpiItem $kpi_item
     * @param Carbon $date
     * @param array &$item
     *
     * @return float
     */
    private function takeRentability(KpiItem $kpi_item, Carbon $date, array &$item): void
    {
        $activity = null;
        if ($kpi_item->activity_id) $activity = Activity::query()->find($kpi_item->activity_id);


        if ($activity
            && $activity->view == Activity::VIEW_RENTAB) {
            $item['fact'] = AnalyticStat::getRentability(
                $activity->group_id,
                $date->format('Y-m-d')
            );

            $item['fact'] = round($item['fact'], 2);
            $item['avg'] = $item['fact'];
        }

    }

    /**
     * take cell value from analytics
     * for kpi item
     *
     * @param $kpi_item_id
     * @param $activity_id
     * @param Carbon $date
     * @param array &$item
     * @param int $user_id
     *
     * @return void
     */
    private function takeUpdatedValue(
        $kpi_item_id,
        $activity_id,
        Carbon $date,
        array &$item,
        int $user_id
    ): void
    {
        $has = $this->updatedValues
            ->where('user_id', $user_id)
            ->where('kpi_item_id', $kpi_item_id)
            ->where('activity_id', $activity_id)
            ->first();

        if ($has) {
            $item['fact'] = (float)$has->value;
            $item['avg'] = (float)$has->value;
        }
    }

    private function calculatePercent(array $item): array
    {
        $item['percent'] = 0;
        $item['plan'] = floatval($item['plan']);
        $item['fact'] = floatval($item['fact']);
        if ($item['method'] == 1) {
            if ($item['plan'] != 0) {
                $item['percent'] = round(($item['fact'] * 100) / $item['plan'], 2);
            } else {
                $item['percent'] = 0;
            }
        } elseif ($item['method'] == 2) {
            $item['percent'] = $item['plan'] ? round(($item['avg'] * 100) / $item['plan'], 2) : 0;
        } elseif ($item['method'] == 3 || $item['method'] == 4) {
            $item['percent'] = $item['avg'] < $item['plan'] ? 100 : 0;
        } elseif ($item['method'] == 5 || $item['method'] == 6) {
            $item['percent'] = $item['avg'] > $item['plan'] ? 100 : 0;
        }
        return $item;
    }

    public function fetchKpisWithCurrency(array $filters = [], bool $limitForProfile = true): array
    {
        if (
            isset($filters['data_from']['year'])
            && isset($filters['data_from']['month'])
        ) {
            $date = Carbon::createFromDate(
                $filters['data_from']['year'],
                $filters['data_from']['month'],
                1
            );
        } else {
            $date = Carbon::now()->setTimezone('Asia/Almaty')->startOfMonth();
        }

        $user_id = $filters['user_id'] ?? 0;
        $currency = 'kzt';

        $this->workdays = collect($this->userWorkdays($filters));
        $this->updatedValues = UpdatedUserStat::query()
            ->whereMonth('date', $date->month)
            ->whereYear('date', $date->year)
            ->orderBy('date', 'desc')
            ->get();

        $last_date = Carbon::parse($date)->endOfMonth()->format('Y-m-d');

        $kpis = Kpi::withTrashed()
            ->with([
                'histories_latest' => function ($query) use ($last_date, $date) {
                    $query->whereDate('created_at', '<=', $last_date);
                },
                'items.histories_latest' => function ($query) use ($last_date, $date) {
                    $query->whereDate('created_at', '<=', $last_date);
                },
                'items' => function (HasMany $query) use ($last_date, $date) {
                    $query->with(['histories' => function (MorphMany $query) use ($last_date, $date) {
                        $query->whereDate('created_at', '<=', $last_date);
                    }]);
                    $query->where(function (Builder $query) use ($last_date) {
                        $query->whereNull('deleted_at');
                        $query->orWhere('deleted_at', '>', $last_date);
                    });
                },
                'items.activity',
                'groups',
                'users',
                'positions'
            ]);

        $user = User::withTrashed()->with([
            'groups',
            'profile_histories_latest' => function ($query) use ($last_date) {
                $query->whereDate('created_at', '<=', $last_date);
            },
        ])->find($user_id);
        $currency = $user->currency;

        if (!$user->profile_histories_latest) {
            $position_id = $user->position_id;
        } else {
            $payload = json_decode($user->profile_histories_latest?->payload, true) ?? [];
            $position_id = $payload['position_id'];
        }

        $activeGroups = ($user->inGroups())->pluck('id')->toArray();
        $droppedGroups = $user->droppedGroups($date);
        $firedGroups = $user->firedGroups();

        $groups = array_merge($activeGroups, $droppedGroups);
        $groups = array_merge($groups, $firedGroups);

        //// get user kpis
        $kpis->withCount([
            'users as has_user' => function ($q) use ($user_id) {
                $q->where('kpiables.kpiable_id', $user_id)
                    ->where('kpiables.kpiable_type', User::class);
            },
            'groups as has_group' => function ($q) use ($activeGroups) {
                $q->whereIn('kpiables.kpiable_id', $activeGroups)
                    ->where('kpiables.kpiable_type', ProfileGroup::class);
            },
            'groups as has_dropped_group' => function ($q) use ($droppedGroups) {
                $q->whereIn('kpiables.kpiable_id', $droppedGroups)
                    ->where('kpiables.kpiable_type', ProfileGroup::class);
            },
            'positions as has_position' => function ($q) use ($position_id) {
                $q->where('kpiables.kpiable_id', $position_id)
                    ->where('kpiables.kpiable_type', Position::class);
            }
        ])->where(function ($query) use ($user_id, $groups, $position_id) {
            $query->whereHas('targetable', function ($q) use ($position_id, $groups, $user_id) {
                if ($q->getModel() instanceof User) {
                    $q->where('targetable_id', $user_id)
                        ->where('targetable_type', User::class);
                } elseif ($q->getModel() instanceof Position) {
                    $q->where('targetable_id', $position_id)
                        ->where('targetable_type', Position::class);
                } elseif ($q->getModel() instanceof ProfileGroup) {
                    $q->whereIn('targetable_id', $groups)
                        ->where('targetable_type', ProfileGroup::class);
                }
            })
                ->orWhereHas('users', function ($q) use ($user_id) {
                    $q->where(function ($q) use ($user_id) {
                        $q->where('kpiables.kpiable_id', $user_id)
                            ->where('kpiables.kpiable_type', User::class);
                    });
                })
                ->orWhereHas('groups', function ($q) use ($groups) {
                    $q->where(function ($q) use ($groups) {
                        $q->whereIn('kpiables.kpiable_id', $groups)
                            ->where('kpiables.kpiable_type', ProfileGroup::class);
                    });
                })
                ->orWhereHas('positions', function ($q) use ($position_id) {
                    $q->where(function ($q) use ($position_id) {
                        $q->where('kpiables.kpiable_id', $position_id)
                            ->where('kpiables.kpiable_type', Position::class);
                    });
                });
        });

        $kpis = $kpis
            ->whereDate('created_at', '<=', Carbon::parse($date->format('Y-m-d'))
                ->endOfMonth()
                ->format('Y-m-d')
            )->where(fn($query) => $query->whereNull('deleted_at')->orWhere(
                fn($query) => $query->whereDate('deleted_at', '>', Carbon::parse($date->format('Y-m-d'))
                    ->endOfMonth()
                    ->format('Y-m-d')))
            )
            ->whereNot(function (Builder $query) use ($date) {
                $query->where('targetable_type', 'App\\User')
                    ->whereHas('user', function (Builder $query) use ($date) {
                        $query->where('deleted_at', '<', Carbon::parse($date->format('Y-m-d'))
                            ->endOfMonth()
                            ->format('Y-m-d'));
                    });
            })
            ->get();

        $kpis = $kpis->filter(function ($kpi) use ($droppedGroups, $activeGroups, $position_id, $user_id) {
            // This code supports old and new relations
            // set priority and target for fetch only latest one or dropped group kpi (LEARN which kpis should be seen in profile!)
            if ($kpi->has_user > 0) {
                $kpi->priority = 1;
                $kpi->targetable_id = $user_id;
                $kpi->targetable_type = 'App\User';
                $kpi->targetable = $kpi->users->where('id', $user_id)->first();
            } elseif ($kpi->has_position > 0) {
                $kpi->priority = 2;
                $kpi->targetable_id = $position_id;
                $kpi->targetable_type = 'App\Position';
                $kpi->targetable = $kpi->positions->where('id', $position_id)->first();
            } elseif ($kpi->has_group > 0) {
                $kpi->priority = 3;
                $kpi->targetable_type = 'App\ProfileGroup';
                $kpi->targetable = $kpi->groups->whereIn('id', $activeGroups)->first();
                $kpi->targetable_id = $kpi->targetable->id;
            } elseif ($kpi->has_dropped_group > 0) {
                $kpi->priority = 4;
                $kpi->targetable_type = 'App\ProfileGroup';
                $kpi->targetable = $kpi->groups->whereIn('id', $droppedGroups)->first();
                $kpi->targetable_id = $kpi->targetable->id;
            } elseif ($kpi->targetable) {
                // This is for previous relation!
                if ($kpi->targetable_type == 'App\User') {
                    $kpi->priority = 1;
                    $kpi->targetable_id = $user_id;
                } elseif ($kpi->targetable_type == 'App\Position') {
                    $kpi->priority = 2;
                    $kpi->targetable_id = $position_id;
                } elseif ($kpi->targetable_type == 'App\ProfileGroup') {
                    $kpi->targetable_id = $kpi->targetable->id;
                    if (in_array($kpi->targetable_id, $activeGroups)) {
                        $kpi->priority = 3;
                    } else {
                        // for dropped group
                        $kpi->priority = 4;
                    }
                }
            } else {
                $kpi->priority = 9999; // chtobi ne meshat v sortirovke
            }


            $history = $kpi->histories_latest;

            if (!$history) {
                return true;
            }

            $payload = json_decode($history->payload, true) ?? [];

            return !isset($payload['is_active']) || $payload['is_active'] != 0;
        });

        if ($limitForProfile && $kpis->count() > 1) {
            $currentKpi = $kpis->sortBy('priority')->first();
            if ($currentKpi->priority != 4) {
                $droppedGroupKpis = $kpis->where('priority', 4);// get dropped group kpis
                $kpis = $droppedGroupKpis->push($currentKpi)->sortBy('priority')->values();
            } else {
                $kpis = collect([$currentKpi]);
            }
        }

        $read = $kpis->contains(fn($k) => in_array($user_id, $k->read_by ?? []));
        $currency_rate = (float)(Currency::rates()[$currency] ?? 0.00001);

        foreach ($kpis as $kpi) {
            $kpi->kpi_items = [];

            // remove items if it's not in history
            if ($kpi->histories_latest) {
                $payload = json_decode($kpi->histories_latest->payload, true);

                if (isset($payload['children'])) {
                    $kpi->items = $kpi->items->whereIn('id', $payload['children']);
                }
                $kpi->completed_80 = $payload['completed_80'];
                $kpi->completed_100 = $payload['completed_100'];
            }

            foreach ($kpi->items as $item) {
                $history = $item->histories->sortByDesc('id')->first();
                $has_edited_plan = $history ? json_decode($history->payload, true) : false;
                $item['daily_plan'] = (float)$item->plan;
                if ($has_edited_plan) {
                    if (array_key_exists('plan', $has_edited_plan)) $item['daily_plan'] = $has_edited_plan['plan'];
                    if (array_key_exists('name', $has_edited_plan)) $item['name'] = $has_edited_plan['name'];
                    if (array_key_exists('share', $has_edited_plan)) $item['share'] = $has_edited_plan['share'];
                    if (array_key_exists('method', $has_edited_plan)) $item['method'] = $has_edited_plan['method'];
                    if (array_key_exists('unit', $has_edited_plan)) $item['unit'] = $has_edited_plan['unit'];
                    if (array_key_exists('cell', $has_edited_plan)) $item['cell'] = $has_edited_plan['cell'];
                    if (array_key_exists('common', $has_edited_plan)) $item['common'] = $has_edited_plan['common'];
                    if (array_key_exists('percent', $has_edited_plan)) $item['percent'] = $has_edited_plan['percent'];
                    if (array_key_exists('sum', $has_edited_plan)) $item['sum'] = $has_edited_plan['sum'];
                    if (array_key_exists('group_id', $has_edited_plan)) $item['group_id'] = $has_edited_plan['group_id'];
                    if (array_key_exists('activity_id', $has_edited_plan)) $item['activity_id'] = $has_edited_plan['activity_id'];
                }
                $item['plan'] = $item['daily_plan'];
            }

            unset($kpi->users);
            $kpi->users = $this->getUsersForKpi($kpi, $date, $user_id);
            $kpi_sum = 0;
            foreach ($kpi->users as $user) {
                $kpi_sum = $kpi_sum + $user['avg_percent'];
            }

            $kpi->avg = count($kpi->users) > 0 ? round($kpi_sum / count($kpi->users), 2) : 0; //AVG percent of all KPI of all USERS in GROUP

            $kpi['dropped'] = in_array($kpi->targetable_id, $droppedGroups) ?? true;
        }

        return [
            'items' => $kpis,
            'activities' => Activity::get(),
            'groups' => ProfileGroup::get()->pluck('name', 'id')->toArray(),
            'user_id' => auth()->user() ? auth()->id() : 1,
            'read' => $read,
            'currency' => $currency,
            'currency_rate' => $currency_rate
        ];
    }

    /**
     * Вытащить список Групп и Пользователей с KPI
     *
     * getUsersForKpi($kpi)
     * getUserStats($kpi, $_user_ids, $date)
     * connectKpiWithUserStats(Kpi $kpi, $_users)
     */
    public function fetchKpiGroupsAndUsers(array $filters): array
    {
        $groupId = $filters['group_id'] ?? null;
        $searchWord = $filters['query'] ?? null;
        $date = Carbon::createFromDate(
            $filters['data_from']['year'] ?? now()->year,
            $filters['data_from']['month'] ?? now()->month
        )->startOfMonth();

        $params = [
            'search_world' => $searchWord,
            'group_id' => $groupId,
            'only_active' => true
        ];

        $query = Kpi::withTrashed()
            ->where(function ($query) use ($date) {
                $query->whereNull('kpis.deleted_at');
                $query->orWhere('kpis.deleted_at', '>', $date->format('Y-m-d'));
            });

        $kpis = $this->kpis($date, $params, $query)->paginate();
        $kpis->data = $kpis->getCollection()->makeHidden(['targetable', 'children']);
//        dd_if(auth()->id = 18, $kpis->data);
        foreach ($kpis->items() as $kpi) {
            $kpi->kpi_items = [];

            if ($kpi->histories_latest) {
                $payload = json_decode($kpi->histories_latest->payload, true);

                if (isset($payload['children'])) {
                    $kpi->items = $kpi->items->whereIn('id', $payload['children']);
                }
            }

            foreach ($kpi->items as $item) {
                $history = $item->histories->sortByDesc('id')->first();
                $has_edited_plan = $history ? json_decode($history->payload, true) : false;
                $item['daily_plan'] = (float)$item->plan;
                if ($has_edited_plan) {
                    if (array_key_exists('plan', $has_edited_plan)) $item['daily_plan'] = $has_edited_plan['plan'];
                    if (array_key_exists('name', $has_edited_plan)) $item['name'] = $has_edited_plan['name'];
                    if (array_key_exists('share', $has_edited_plan)) $item['share'] = $has_edited_plan['share'];
                    if (array_key_exists('method', $has_edited_plan)) $item['method'] = $has_edited_plan['method'];
                    if (array_key_exists('unit', $has_edited_plan)) $item['unit'] = $has_edited_plan['unit'];
                    if (array_key_exists('cell', $has_edited_plan)) $item['cell'] = $has_edited_plan['cell'];
                    if (array_key_exists('common', $has_edited_plan)) $item['common'] = $has_edited_plan['common'];
                    if (array_key_exists('percent', $has_edited_plan)) $item['percent'] = $has_edited_plan['percent'];
                    if (array_key_exists('sum', $has_edited_plan)) $item['sum'] = $has_edited_plan['sum'];
                    if (array_key_exists('group_id', $has_edited_plan)) $item['group_id'] = $has_edited_plan['group_id'];
                    if (array_key_exists('activity_id', $has_edited_plan)) $item['activity_id'] = $has_edited_plan['activity_id'];
                }
                $item['plan'] = $item['daily_plan'];
            }

            $kpi->users = $this->getAverageKpiPercent($kpi, $date);
            $kpi_sum = 0;
            foreach ($kpi->users as $user) {
                $kpi_sum = $kpi_sum + $user['avg_percent'];
            }
            $kpi->avg = count($kpi->users) > 0 ? round($kpi_sum / count($kpi->users)) : 0; //AVG percent of all KPI of all USERS in GROUP
//            dd_if(auth()->id() == 5 && $kpi->id == 157, count($kpi->users), $kpi_sum, $kpi->users);
        }

        return [
            'paginator' => $kpis,
            'groups' => ProfileGroup::query()
                ->get()
                ->pluck('name', 'id')
                ->toArray(),
            'user_id' => auth()->user() ? auth()->id() : 1
        ];
    }

    public function kpis(
        Carbon  $date = null,
        array   $filter = [],
        Builder $query = null,
    )
    {
        $searchWord = $filter['search_world'] ?? null;
        $groupId = $filter['group_id'] ?? null;
        $this->workdays = collect($this->userWorkdays(['filters' => $date->startOfMonth()->format("Y-m-d")]));
        $this->updatedValues = UpdatedUserStat::query()
            ->whereMonth('date', $date->month)
            ->whereYear('date', $date->year)
            ->orderBy('date', 'desc')
            ->get();

        $last_date = $date->endOfMonth()->format("Y-m-d");
        $query ?: Kpi::withTrashed();
        return $query
            ->when($groupId, function (Builder $subQuery) use ($groupId) {
                $subQuery->where('targetable_id', $groupId);
                $subQuery->orWhereRelation(
                    relation: 'groups',
                    column: 'kpiable_id',
                    operator: '=',
                    value: $groupId
                );
            })
            ->when($searchWord, fn(Builder $whenQuery) => (new KpiFilter($whenQuery))->globalSearch($searchWord))
            ->with([
                'histories_latest' => function ($query) use ($date) {
                    $query->whereDate('created_at', '<=', $date);
                },
                'items.histories_latest' => function ($query) use ($date) {
                    $query->whereDate('created_at', '<=', $date);
                },
                'items' => function (HasMany $query) use ($last_date, $date) {
                    $query->with(['histories' => function (MorphMany $query) use ($date) {
                        $query->whereDate('created_at', '<=', $date);
                    }]);
                    $query->where(function (Builder $query) use ($last_date) {
                        $query->whereNull('deleted_at');
                        $query->orWhere('deleted_at', '>', $last_date);
                    });
                },
                'items.activity'
            ])
            ->where(function ($query) use ($last_date) {
                $query->whereHas('targetable', function ($q) use ($last_date) {
                    if ($q->getModel() instanceof User) {
                        $q->whereNull('deleted_at')
                            ->orWhere('deleted_at', '>', $last_date);
                    } elseif ($q->getModel() instanceof Position) {
                        $q->whereNull('deleted_at')
                            ->orWhereDate('deleted_at', '>', $last_date);
                    }
                })
                    ->orWhereHas('users', fn($q) => $q->whereNull('deleted_at')
                        ->orWhereDate('deleted_at', '>', $last_date))
                    ->orWhereHas('positions', fn($q) => $q->whereNull('deleted_at')
                        ->orWhereDate('deleted_at', '>', $last_date))
                    ->orWhereHas('groups');
            })
            ->with([
                'users' => fn($q) => $q->whereNull('deleted_at')
                    ->orWhereDate('deleted_at', '>=', $last_date),
                'positions' => fn($q) => $q->whereNull('deleted_at')
                    ->orWhereDate('deleted_at', '>=', $last_date),
                'groups' => fn($q) => $q->where('active', 1),
            ])
            ->whereDate('kpis.created_at', '<=', $last_date)
            ->distinct();
    }

    public function getAverageKpiPercent(Kpi $kpi, Carbon $date): array
    {
        $dateFrom = $date->copy()->startOfMonth();
        $dateTo = $date->copy()->endOfMonth();
        // check target exists
        if (!$kpi->target) return [];

        $type = $kpi->target['type'];

        // User::class
        if ($type == 1) {
            $_user_ids = [$kpi->targetable_id];
        }

        // ProfileGroup::class
        if ($type == 2) {
            $profileGroup = ProfileGroup::query()->findOrFail($kpi->targetable_id);
            $_user_ids = collect((new UserService)->getEmployees($profileGroup->id, $date->toDateString()))->whereNull('deleted_at')->pluck('id')->toArray();
        }

        // Position::class
        if ($type == 3) {
            $_user_ids = User::withTrashed()
                ->where(function (Builder $query) use ($dateTo) {
                    $query->whereNull('deleted_at');
                    $query->orWhere('deleted_at', '>=', $dateTo->format("Y-m-d"));
                })
                ->with(['profile_histories_latest' => function ($query) use ($dateFrom, $dateTo) {
                    $query->whereBetween('created_at', [$dateFrom->format("Y-m-d"), $dateTo->format("Y-m-d")]);
                }])
                ->get()
                ->filter(function (User $user) use ($kpi) {
                    $history = $user->profile_histories_latest;
                    if ($history) {
                        $positionsId = json_decode($history->payload, true)['position_id'];
                        return $positionsId == $kpi->targetable_id;
                    }
                    return $user->position_id == $kpi->targetable_id;
                })
                ->pluck('id')
                ->toArray();
        }
        $_users = $this->getUserStats($kpi, $_user_ids, $date);

        // create final users array
        return $this->getKpiStats(
            $kpi,
            $_users,
            $date,
        );
    }

    /**
     * Get AVG KPI percent for all users
     *
     * @param Kpi $kpi
     * @param mixed $_users
     * @param Carbon $date
     * @return array
     */
    private
    function getKpiStats(
        Kpi    $kpi,
        mixed  $_users,
        Carbon $date,
    ): array
    {
        // fill users array
        $users = [];

        foreach ($_users as $user) {
            $sumKpiPercent = 0;

            foreach ($kpi->items as $_item) {

                // to array because object changes every loop
                $item = $_item->toArray();

                // Last History
                $histories_latest = $_item->histories_latest;
                if ($histories_latest) {
                    $historyPayload = json_decode($histories_latest->payload, true);

                    if (Arr::exists($historyPayload, 'activity_id')) $item['activity_id'] = $historyPayload['activity_id'];
                    if (Arr::exists($historyPayload, 'plan')) $item['plan'] = $historyPayload['plan'];
                }

                $item['plan'] = $user['full_time'] ? $item['plan'] : $item['plan'] / 2;

                $exists = collect($user['items'])
                    ->where('activity_id', $item['activity_id'])
                    ->first();

                // assign keys
                if ($exists) {
                    $item['fact'] = $exists->fact;
                    $item['avg'] = $exists->avg;
                    $item['records_count'] = $exists->records_count;
                    $item['days'] = $exists->days;
                    $item['registered'] = $exists->registered_days;
                    $item['applied'] = $exists->applied;

                    if ($_item->activity
                        && $_item->activity->view == Activity::VIEW_QUALITY) {

                        $query = UserStat::query()
                            ->selectRaw("
                                value,
                                activity_id,
                                user_id,
                                DAY(date) as day
                            ")
                            ->whereMonth('date', $date->month)
                            ->whereYear('date', $date->year)
                            ->where('activity_id', $_item->activity_id)
                            ->where('user_id', $user['id'])
                            ->get();

                        /**
                         * if avg methods
                         * take weeks
                         *
                         */
                        if (in_array($_item->method, [2, 4, 6])) {
                            $weeks = $this->weeksArray($date->month, $date->year);

                            /**
                             * count avg of every user
                             */

                            $avg = 0;
                            $count = 0;

                            foreach ($weeks as $key => $week) {
                                $val = isset($week[0])
                                    ? $query->whereBetween('day', [$week[0], $week[count($week) - 1]])->avg('value')
                                    : 0;

                                if ($val && $val > 0) {
                                    $avg += $val;
                                    $count++;
                                }
                            }

                            $item['fact'] = $query->sum('value');
                            $item['avg'] = $count > 0 ? round($avg / $count, 2) : 0;
                            $item['records_count'] = $count;

                        } else {
                            $item['fact'] = $query->sum('value');
                            $item['avg'] = $query->avg('avg');
                            $item['records_count'] = $query->count();
                        }

                    }

                } else {
                    $item['fact'] = 0;
                    $item['avg'] = 0;
                    $item['records_count'] = 0;
                    $item['days'] = 0;
                    $item['registered'] = 0;
                    $item['applied'] = null;
                }

                /**
                 * take another activity values
                 */
                $item['fact'] = $item['fact'] ?? 0;

                $this->takeCommonValue($_item, $date, $item);
                $this->takeUpdatedValue($_item->id,
                    $item['activity_id'],
                    $date,
                    $item,
                    $user['id']
                );
                $item = $this->calculatePercent($item);
//                dd_if($user['id'] == 1739, $item['percent'], count($kpi->items));

                $sumKpiPercent = $sumKpiPercent + $item['percent'];
            }

            /**
             * add user to final array
             */
            $kpiItemsCount = count($kpi->items);
            $user['avg_percent'] = $kpiItemsCount > 0
                ? round($sumKpiPercent / $kpiItemsCount, 2)
                : 0;
            $users[] = $user;
        }

        return $users;
    }

    /**
     * Вытащить список kpi со статистикой
     *
     * getUsersForKpi($kpi)
     * getUserStats($kpi, $_user_ids, $date)
     * connectKpiWithUserStats(Kpi $kpi, $_users)
     */
    public function fetchKpiGroupOrUser(array $request, int $targetableId): array
    {
        $this->dateFromRequest($request);
        $targetableType = self::TARGET_TYPES[$request['type']];

        $this->workdays = collect($this->userWorkdays($request['filters']));
        $this->updatedValues = UpdatedUserStat::query()
            ->whereMonth('date', $this->from->month)
            ->whereYear('date', $this->from->year)
            ->orderBy('date', 'desc')
            ->get();
//dd($this->updatedValues->where('user_id', 6401)->where('kpi_item_id', 601)->first()->toArray());
        /** @var Kpi $kpi */
        $kpi = Kpi::withTrashed()
            ->with([
                'histories_latest' => function ($query) {
                    $query->whereDate('created_at', '<=', $this->to);
                },
                'items.histories_latest' => function ($query) {
                    $query->whereDate('created_at', '<=', $this->to);
                },
                'items' => function (HasMany $query) {
                    $query->with(['histories' => function (MorphMany $query) {
                        $query->whereDate('created_at', '<=', $this->to);
                    }]);
                    $query->where(function (Builder $query) {
                        $query->whereNull('deleted_at');
                        $query->orWhere('deleted_at', '>', $this->to);
                    });
                },
                'items.activity'
            ])
            ->where('created_at', '<=', $this->to)
            ->where(fn($query) => $query->whereNull('deleted_at')
                ->orWhere(
                    fn($query) => $query->whereDate('deleted_at', '>', $this->from)
                )
            )
            ->where(function (Builder $query) use ($targetableType, $targetableId) {
                $query->where(function (Builder $query) use ($targetableType, $targetableId) {
                    $query->where('targetable_id', $targetableId);
                    $query->where('targetable_type', $targetableType);
                });
                $query->orWhere(function (Builder $query) use ($targetableType, $targetableId) {
                    $query->whereHas('users', fn($q) => $q
                        ->where('kpi_id', $targetableId)
                        ->whereNull('deleted_at')
                        ->orWhereDate('deleted_at', '>', $this->from));
                    $query->orWhereHas('positions', fn($q) => $q
                        ->where('kpi_id', $targetableId)
                        ->whereNull('deleted_at')
                        ->orWhereDate('deleted_at', '>', $this->from));
                    $query->orWhereHas('groups', fn($q) => $q
                        ->where('kpi_id', $targetableId)
                        ->where('active', 1));
                });
            })
            ->firstOrFail();

        if ($kpi->histories_latest) {
            $payload = json_decode($kpi->histories_latest->payload, true);

            if (isset($payload['children'])) {
                $kpi->items = $kpi->items->whereIn('id', $payload['children']);
            }
            $kpi->completed_80 = $payload['completed_80'];
            $kpi->completed_100 = $payload['completed_100'];
        }

        foreach ($kpi->items as $item) {
            $history = $item->histories->sortByDesc('id')->first();
            $has_edited_plan = $history ? json_decode($history->payload, true) : false;
            $item['daily_plan'] = (float)$item->plan;
            if ($has_edited_plan) {
                if (array_key_exists('plan', $has_edited_plan)) $item['daily_plan'] = $has_edited_plan['plan'];
                if (array_key_exists('name', $has_edited_plan)) $item['name'] = $has_edited_plan['name'];
                if (array_key_exists('share', $has_edited_plan)) $item['share'] = $has_edited_plan['share'];
                if (array_key_exists('method', $has_edited_plan)) $item['method'] = $has_edited_plan['method'];
                if (array_key_exists('unit', $has_edited_plan)) $item['unit'] = $has_edited_plan['unit'];
                if (array_key_exists('cell', $has_edited_plan)) $item['cell'] = $has_edited_plan['cell'];
                if (array_key_exists('common', $has_edited_plan)) $item['common'] = $has_edited_plan['common'];
                if (array_key_exists('percent', $has_edited_plan)) $item['percent'] = $has_edited_plan['percent'];
                if (array_key_exists('sum', $has_edited_plan)) $item['sum'] = $has_edited_plan['sum'];
                if (array_key_exists('group_id', $has_edited_plan)) $item['group_id'] = $has_edited_plan['group_id'];
                if (array_key_exists('activity_id', $has_edited_plan)) $item['activity_id'] = $has_edited_plan['activity_id'];
            }
            $item['plan'] = $item['daily_plan'];
        }

        $kpi->users = $this->getUsersForKpi($kpi, $this->from);
        $kpi_sum = 0;

        foreach ($kpi->users as $user) {
            $kpi_sum = $kpi_sum + $user['avg_percent'];
        }

        $kpi->avg = count($kpi->users) > 0 ? round($kpi_sum / count($kpi->users)) : 0; //AVG percent of all KPI of all USERS in GROUP

        return [
            'kpi' => $kpi,
            'user_id' => auth()->user() ? auth()->id() : 1
        ];
    }

    private function dateFromRequest(array $request): void
    {
        $year = $request['filters']['data_from']['year'] ?? now()->year;
        $month = $request['filters']['data_from']['month'] ?? now()->month;
        $day = $request['filters']['data_from']['day'] ?? now()->day;
        $this->from = Carbon::createFromDate($year, $month, $day)->startOfMonth();
        $this->to = Carbon::createFromDate($year, $month, $day)->endOfMonth();
    }

    /**
     * Возвращает процент выполнения KPI по месяцам года
     */
    public function fetchAnnualKPIPercent(Request $request): array
    {
        $limit = $request->limit ? $request->limit : 10;
        $year = $request->year ? $request->year : date("Y");

        for ($month = 1; $month <= 12; $month++) {

            $date = Carbon::createMidnightDate($year, $month, 1);
            $firstDayOfCurrentMonth = Carbon::now()->startOfMonth();

            if ($date > $firstDayOfCurrentMonth) {
                continue;
            }

            $this->updatedValues = UpdatedUserStat::query()
                ->whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->orderBy('date', 'desc')
                ->get();

            $last_date = Carbon::parse($date)->endOfMonth()->format('Y-m-d');
            $kpis = Kpi::withTrashed()
                ->with([
                    'histories_latest' => function ($query) use ($last_date) {
                        $query->whereDate('created_at', '<=', $last_date);
                    },
                    'items.histories_latest' => function ($query) use ($last_date) {
                        $query->whereDate('created_at', '<=', $last_date);
                    },
                    'items' => function ($query) use ($last_date) {
                        $query->withTrashed()->whereDate('created_at', '<=', $last_date);
                    },
                    'items.activity'
                ])
                ->whereDate('created_at', '<=', Carbon::parse($date->format('Y-m-d'))
                    ->endOfMonth()
                    ->format('Y-m-d')
                )->where(fn($query) => $query->whereNull('deleted_at')->orWhere(
                    fn($query) => $query->whereDate('deleted_at', '>', Carbon::parse($date->format('Y-m-d'))
                        ->endOfMonth()
                        ->format('Y-m-d')))
                )
                ->where('is_active', true)
                ->whereNot(function (Builder $query) use ($date) {
                    $query->where('targetable_type', 'App\\User')
                        ->whereHas('user', function (Builder $query) use ($date) {
                            $query->where('deleted_at', '<', Carbon::parse($date->format('Y-m-d'))
                                ->endOfMonth()
                                ->format('Y-m-d'));
                        });
                })
                ->whereHasMorph(
                    'kpiable',
                    '*',
                    function (Builder $query, string $type) {
                        if ($type === 'App\ProfileGroup') {
                            $query->whereNull('archived_date');
                        }
                    }
                )
                ->paginate($limit);

            $kpis->data = $kpis->makeHidden(['targetable', 'children']);

            $kpisAnnual['current_page'] = $kpis->currentPage();
            $kpisAnnual['last_page'] = $kpis->LastPage();
            $kpisAnnual['links'] = $kpis->linkCollection();
            $kpisAnnual['per_page'] = $kpis->perPage();
            $kpisAnnual['total'] = $kpis->total();

            $cacheKey = $year . '-' . $month;
            $KpiItemsCached = KpiItemsCacheStorage::get($cacheKey);
            if ($KpiItemsCached) {
                $kpisAnnual['data'][$month] = $KpiItemsCached;
                continue;
            }

            foreach ($kpis->items() as $kpi) {

                $kpi->kpi_items = [];

                $histories_latest = $kpi->histories_latest;
                if ($histories_latest) {
                    $payload = json_decode($histories_latest->payload, true);

                    if (isset($payload['children'])) {
                        $kpi->items = $kpi->items->whereIn('id', $payload['children']);
                    }
                }

                $kpi->users = $this->getAverageKpiPercent($kpi, $date);
                $kpi_sum = 0;
                foreach ($kpi->users as $user) {
                    $kpi_sum = $kpi_sum + $user['avg_percent'];
                }
                $kpi->avg = count($kpi->users) > 0 ? round($kpi_sum / count($kpi->users)) : 0; //AVG percent of all KPI of all USERS in GROUP
            }

            $kpisAnnual['data'][$month] = $kpis->items();
            if ($date != $firstDayOfCurrentMonth) {
                KpiItemsCacheStorage::put($cacheKey, $kpisAnnual['data'][$month]);
            }
        }

        return [
            'paginator' => $kpisAnnual
        ];
    }

    /**
     * @param int $user_id
     * @return void
     */
    public
    function readKpis(int $user_id)
    {
        $user = User::find($user_id);

        if (is_null($user)) {
            throw new HttpException("Пользователь не найден");
        }

        $position_id = $user->position_id;
        $groups = $user->groups->pluck('id')->toArray();

        $kpis = Kpi::withTrashed()->where(function ($query) use ($user_id, $groups, $position_id) {
            $query->where(function ($q) use ($user_id) {
                $q->where('targetable_id', $user_id)
                    ->where('targetable_type', self::USER);
            })
                ->orWhere(function ($q) use ($groups) {
                    $q->whereIn('targetable_id', $groups)
                        ->where('targetable_type', self::PROFILE_GROUP);
                })
                ->orWhere(function ($q) use ($position_id) {
                    $q->where('targetable_id', $position_id)
                        ->where('targetable_type', self::POSITION);
                });
        })->get();

        foreach ($kpis as $kpi) {
            $read_by = $kpi->read_by ?? [];
            if (!in_array($user_id, $read_by)) {
                $read_by[] = $user_id;
                $kpi->update(['read_by' => $read_by]);
            }
        }
    }


    /**
     * @param int $user_id
     * @return void
     */
    public
    function readQuartalPremiums(int $user_id)
    {
        $user = User::find($user_id);

        if (is_null($user)) {
            throw new HttpException("Пользователь не найден");
        }

        $position_id = $user->position_id;
        $groups = $user->groups->pluck('id')->toArray();

        $qps = QuartalPremium::withTrashed()->with('activity')->where(function ($query) use ($user_id, $groups, $position_id) {
            $query->where(function ($q) use ($user_id) {
                $q->where('targetable_id', $user_id)
                    ->where('targetable_type', 'App\User');
            })
                ->orWhere(function ($q) use ($groups) {
                    $q->whereIn('targetable_id', $groups)
                        ->where('targetable_type', 'App\ProfileGroup');
                })
                ->orWhere(function ($q) use ($position_id) {
                    $q->where('targetable_id', $position_id)
                        ->where('targetable_type', 'App\Position');
                });
        })->get();

        foreach ($qps as $qp) {
            $read_by = $qp->read_by ?? [];
            if (!in_array($user_id, $read_by)) {
                $read_by[] = $user_id;
                $qp->update(['read_by' => $read_by]);
            }
        }
    }

    /**
     * Получаем кв-премий групповые.
     */
    private function getProfileGroupQp($quartalPremium)
    {
        return ProfileGroup::query()
            ->select(DB::raw('CONCAT(u.name,\' \',u.last_name) as full_name'), 'us.user_id', 'us.activity_id', 'profile_groups.name', DB::raw('SUM(us.value) as fact'))
            ->join('group_user as gu', 'gu.group_id', '=', 'profile_groups.id')
            ->join('users as u', 'u.id', '=', 'gu.user_id')
            ->join('user_stats as us', 'us.user_id', '=', 'u.id')
            ->where('profile_groups.id', $quartalPremium->targetable_id)
            ->where('us.activity_id', '=', $quartalPremium->activity_id)
            ->whereBetween('us.date', [$quartalPremium->from, $quartalPremium->to])
            ->groupBy(['us.activity_id', 'us.user_id', 'profile_groups.name', 'u.name', 'u.last_name'])
            ->get()->each(function ($data) use ($quartalPremium) {
                $data->targetable_id = $quartalPremium->targetable_id;
                $data->targetable_type = $quartalPremium->targetable_type;
                $data->expended = false;
                $data->quartalPremiums = [
                    'title' => $quartalPremium->title,
                    'text' => $quartalPremium->text,
                    'plan' => $quartalPremium->plan,
                    'from' => $quartalPremium->from,
                    'to' => $quartalPremium->to,
                    'sum' => $quartalPremium->sum,
                ];
            });
    }

    /**
     * take timeboard value from analytics
     * for kpi item
     *
     * @param KpiItem $kpi_item
     * @param Carbon $date
     * @param array &$item
     *
     * @return void
     */
    private
    function takeTimeboardValue(KpiItem $kpi_item, Carbon $date, array &$item): void
    {
        if ($kpi_item->activity
            && $kpi_item->activity->source == Activity::SOURCE_TIMEBOARD) {
            $item['fact'] = round($item['fact'], 2);
        }
    }

    /**
     * take HR value from analytics
     * for kpi item
     *
     * @param KpiItem $kpi_item
     * @param Carbon $date
     * @param array &$item
     *
     * @return void
     */
    private
    function takeHRValue(KpiItem $kpi_item, Carbon $date, array &$item): void
    {
        if ($kpi_item->activity
            && $kpi_item->activity->source == Activity::SOURCE_HR) {
            $item['fact'] = round($item['fact'], 2);
        }
    }
}
