<?php

namespace App\Service;

use App\Http\Requests\BonusesFilterRequest;
use App\Models\GroupUser;
use App\Models\Kpi\Bonus;
use App\Models\QuartalPremium;
use App\Position;
use App\Traits\KpiHelperTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use App\User;
use App\ProfileGroup;
use App\Models\Kpi\KpiItem;
use App\Models\Kpi\Kpi;
use App\Models\Analytics\UserStat;
use App\Models\Analytics\UpdatedUserStat;
use App\Models\Analytics\Activity;
use App\Models\Analytics\AnalyticStat;

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

    /**
     * Workdays for kpi_items
     */
    public $workdays;

    /**
     * Workdays for kpi_items
     */
    public $updatedValues;


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
        $date   = $request->input('date');

        return $this->calculateStatistics($user, $method, $date);
    }

    public function fetch(Request $request)
    {
        $parameters = $request->all();
        $groupId    = $parameters['filter']['group_id'] ?? null;

        $group      = ProfileGroup::query()->findOrFail($groupId);
        $users      = $this->getUserKpis($group, $parameters);
        return $users;
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

        foreach ($userStats as $userStat)
        {
            foreach ($kpItems as $userPlan)
            {
                if ($userStat['activity_id'] == $userPlan['activity_id']){
                    $workdays = $userStat['working_day_id'] != 1 ? [6,0] : [0];
                    $statistics[] = [
                        'kpi_id'                    => $userPlan['kpi_id'],
                        'activity_id'               => $userStat['activity_id'],
                        'daily_plan'                => $userPlan['plan'],
                        'total_fact'                => $userStat['total_fact'],
                        'is_user_full_time'         => $userStat['full_time'],
                        'workdays'                  => workdays(date('Y'), date('m'), $workdays),
                        'days_from_user_applied'    => 0,
                        'records_count'             => $this->getRecordsCount($date, $userId),
                        'lower_limit'               => $userPlan['lower_limit'],
                        'upper_limit'               => $userPlan['upper_limit'],
                        'share'                     => $userPlan['share'],
                        'completed_80'              => $userPlan['completed_80'],
                        'completed_100'             => $userPlan['completed_100']
                    ];
                }
            }
        }

        return $statistics;
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
        $statistics = $this->getUserStatistics((int) $user->id, $date);
        $calculateKpi = new CalculateKpiService();

        foreach ($statistics as $index => $statistic)
        {
            $statistics[$index]['percent']    = $calculateKpi->getCompletePercent($statistic, $method);
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
     * @param $group
     * @param $parameters
     * @return array
     */
    private function getUserKpis($group, $parameters): array
    {
        $userIds = $group->users()->pluck('id')->toArray();

        return User::query()->whereIn('id', $userIds)->whereHas('kpis')->with([
            'kpis' => function($kpi) use($parameters) {
                switch (isset($parameters['filter']['created_at']['variant'])){
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
        int $lower_limit,
        int $upper_limit,
        float $completed_percent,
        int $share,
        float $completed_80,
        float $completed_100
    ) : float|int {
        $result = 0;
        $completed_percent = 80;
        $lower_limit = $lower_limit / 100;
        $upper_limit = $upper_limit / 100;
        $completed_percent = $completed_percent / 100;
        $share = $share / 100;

        if($completed_percent > $lower_limit) {
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
    public function fetchBonuses(BonusesFilterRequest $request): array
    {
        $bonuses         = $this->getBonuses($request);
        $profileGroupIds = [];
        $userIds         = [];
        $positionIds     = [];

        foreach ($bonuses as $bonus)
        {
            if ($bonus->targetable_type == self::PROFILE_GROUP)
            {
                $profileGroupIds[] = $bonus->targetable_id;
            }
            if ($bonus->targetable_type == self::POSITION)
            {
                $positionIds[] = $bonus->targetable_id;
            }
            if ($bonus->targetable_type == self::USER)
            {
                $userIds[] = $bonus->targetable_id;
            }
        }

         return [
            'groups' => $this->getProfileGroupBonus($profileGroupIds, $request) ?? null,
            'positions' => $this->getPositionBonus($positionIds, $request) ?? null,
            'users' => $this->getUserBonus($userIds, $request) ?? null
        ];
    }

    /**
     * Получаем сотрудников.
     * @param $userIds
     * @param $request
     * @return Builder[]|Collection
     */
    private function getUserBonus($userIds, $request)
    {
        $month  = $request->month ?? null;
        $year   = $request->year ?? null;

        return User::with([
            'obtainedBonuses.bonus' => fn ($bonus) => $bonus->when($year && $month, fn ($bonus) => $bonus->whereYear('created_at', $year)->whereMonth('created_at', $month))
        ])->whereIn('id', $userIds)->get();
    }

    /**
     * Получаем по позициям.
     * @param $positionIds
     * @param $request
     * @return Builder[]|Collection
     */
    private function getPositionBonus($positionIds, $request)
    {
        $userId = $request->user_id ?? null;
        $month  = $request->month ?? null;
        $year   = $request->year ?? null;

        return Position::with([
            'users' => fn($user) => $user->when($userId, fn($user) => $user->where('id', $userId)),
            'users.obtainedBonuses.bonus' => fn ($bonus) => $bonus->when($year && $month, fn ($bonus) => $bonus->whereYear('created_at', $year)->whereMonth('created_at', $month))
        ])->whereIn('id', $positionIds)->get();
    }

    /**
     * Получаем по группам.
     * @param $groupIds
     * @param $request
     * @return Builder[]|Collection
     */
    private function getProfileGroupBonus($groupIds, $request)
    {
        $userId = $request->user_id ?? null;
        $month  = $request->month ?? null;
        $year   = $request->year ?? null;

        return ProfileGroup::with([
            'users' => fn($user) => $user->when($userId, fn($user) => $user->where('id', $userId)),
            'users.obtainedBonuses.bonus' => fn ($bonus) => $bonus->when($year && $month, fn ($bonus) => $bonus->whereYear('created_at', $year)->whereMonth('created_at', $month))
        ])->whereIn('id', $groupIds)->get();
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getBonuses(Request $request)
    {
        $parameters = $request->all();
        $type       = isset($parameters['targetable_type']) ? $this->getModel($parameters['targetable_type']) : null;
        $id         = $parameters['targetable_id'] ?? null;

        return Bonus::withTrashed()->when(isset($type) && isset($id), fn($kpi) => $kpi->where([
            ['targetable_type', $type],
            ['targetable_id', $id]
        ]))->paginate(50);
    }

    /**
     * Список Квартальных премии
     */
    public function fetchQuartalPremiums(Request $request)
    {
        $quartalPremiums = $this->getQuartalPremiums($request);
        $usersId         = [];
        $profileGroupsId = [];
        $positionsId     = [];

        foreach ($quartalPremiums as $quartalPremium)
        {
            if ($quartalPremium->targetable_type == self::USER)
            {
                $usersId[] = $quartalPremium->targetable_id;
            }

            if ($quartalPremium->targetable_type == self::PROFILE_GROUP)
            {
                $profileGroupsId[] = $quartalPremium->id;
            }

            if ($quartalPremium->targetable_type == self::POSITION)
            {
                $positionsId[] = $quartalPremium->id;
            }
        }

        return [
            self::USER => $this->getUsersQp($usersId)
        ];
    }

    private function getUsersQp($ids)
    {
        return User::query()->with(['statistics' => function($statistic) {
            dd($statistic->select('activity_id', 'user_id', DB::raw("SUM(value)"))->groupBy('activity_id', 'user_id')->get());
        }])->whereIn('id', $ids)->get();
    }

    private function getQuartalPremiums(Request $request)
    {
        $type = isset($request->targetable_type) ? $this->getModel($request->targetable_type) : null;
        $id   = $request->targetable_id ?? null;

        return QuartalPremium::withTrashed()->when(isset($type) && isset($id), fn($kpi) => $kpi->where([
            ['targetable_type', $type],
            ['targetable_id', $id]
        ]))->get();
    }

    /**
     * Вытащить список kpi со статистикой
     *
     * getUsersForKpi($kpi)
     * getUserStats($kpi, $_user_ids, $date)
     * connectKpiWithUserStats(Kpi $kpi, $_users)
     */
    public function fetchKpis(Request $request) : array
    {
        $filters = $request->filters;
        
        /**
         * filters
         *
         * date_from
         * user_id
         */
        if(
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
        
        
        $this->workdays = collect($this->userWorkdays($request));
        $this->updatedValues = UpdatedUserStat::query()
                            ->whereMonth('date', $date->month)
                            ->whereYear('date', $date->year)
                            ->orderBy('created_at', 'desc')
                            ->get();

        /**
         * get kpis
         */

        $kpis = Kpi::query()
            //->withTrashed()
            ->with('items.activity');

        if($user_id != 0) {
            $user = User::with('groups')->find($user_id);
            $groups = $user->groups->pluck('id')->toArray();
            //if($user_id == 16471)dd($kpis->toArray());

            $kpis->where(function($query) use ($user_id) {
                    $query->where('targetable_id', $user_id)
                        ->where('targetable_type', 'App\User');
                })
                ->orWhere(function($query) use ($groups) {
                    $query->whereIn('targetable_id', $groups)
                        ->where('targetable_type', 'App\ProfileGroup');
                });
        }

        $kpis = $kpis->get();

        foreach ($kpis as $key => $kpi) {
            $kpi->kpi_items = [];
            $kpi->avg = 0; // avg percent from kpi_items' percent
            $kpi->users = $this->getUsersForKpi($kpi, $date, $user_id);
        }

        

        return [
            'items' => $kpis,
            'activities' => Activity::get(),
            'groups'     => ProfileGroup::get()->pluck('name', 'id')->toArray(),
        ];
    }

    /**
     * Helper for fetchKpis()
     */
    private function getUsersForKpi(
        Kpi $kpi,
        Carbon $date,
        int $user_id = 0
    ) : array
    {
        // check target exists
        if(!$kpi->target) return [];

        $type = $kpi->target['type'];


        // User::class
        if($type == 1) {
            $_user_ids = [$kpi->targetable_id];
        }

        // ProfileGroup::class
        if($type == 2) {
            $_user_ids = json_decode(ProfileGroup::find($kpi->targetable_id)->users);
            if($user_id != 0)  $_user_ids = in_array($user_id, $_user_ids) ? [$user_id] : [];
        }

        // Position::class
        if($type == 3) $_user_ids = [];

        
        // get users with user stats
        $_users = $this->getUserStats($kpi, $_user_ids, $date);

        // create final users array
        $users = $this->connectKpiWithUserStats(
            $kpi,
            $_users,
            $date,
        );

        return $users;
    }

   

    /**
     * Create final users array
     */
    private function connectKpiWithUserStats(
        Kpi $kpi,
        mixed $_users,
        Carbon $date,
    ) : array
    {

        // count workdays in month
        $workdays = [];
        $workdays[5] = workdays($date->year, $date->month, [6,0]);
        $workdays[6] = workdays($date->year, $date->month, [0]);

        // fill users array
        $users = [];

        foreach ($_users as $key => $user) {
       
            $kpi_items = [];

            foreach ($kpi->items as $key => $_item) {

                // to array because object changes every loop
                $item = $_item->toArray();
                
                // check user stat exists
                $exists = collect($user['items'])
                        ->where('activity_id', $item['activity_id'])
                        ->first();

                // assign keys
                if($exists) {
                    $item['fact']          = $exists->fact;
                    $item['avg']           = $exists->avg;
                    $item['records_count'] = $exists->records_count;
                    $item['days']          = $exists->days;
                    $item['registered']    = $exists->registered_days;
                    $item['applied']       = $exists->applied;
                } else {
                    $item['fact']          = 0;
                    $item['avg']           = 0;
                    $item['records_count'] = 0;
                    $item['days']          = 0;
                    $item['registered']    = 0;
                    $item['applied']       = null;
                }   

                //  take another activity values
                $item['fact'] = $item['fact'] ?? 0;
                $this->takeCommonValue( $_item, $date, $item);
                $this->takeCellValue(   $_item, $date, $item['fact']);
                $this->takeRentability( $_item, $date, $item['fact']);
                $this->takeUpdatedValue($_item, $date, $item['fact'], $user['id']);
               
                // plan

                $item['full_time'] = $user['full_time'];
                $item['daily_plan'] = (float)$_item->plan;
                $item['plan'] = $_item->plan;
                $item['workdays'] = $workdays[6];

               
                if($_item->activity) {
                    $has_workdays = $this->workdays->where('user_id', $user['id'])
                                    ->where('activity_id', $_item->activity->id)
                                    ->first();
                    if($has_workdays) $item['workdays']  = $has_workdays['user_work_days'];
                } 
            
                $kpi_items[] = $item;
            }
            
            $user['items'] = $kpi_items;

       
            $users[] = $user;
        }

        return $users;
    }

    /**
     * If value is from all group
     * take common value made by group
     * 
     * @param KpiItem $kpi_item
     * @param Carbon $date
     * @param array &$item
     * 
     * @return array
     */
    private function takeCommonValue(KpiItem $kpi_item, Carbon $date, array &$item) : void
    {
        if($kpi_item->common == 1) {
            $query = UserStat::selectRaw("
                    SUM(value) as fact,
                    AVG(value) as avg,
                    COUNT(value) as records_count,
                    activity_id,
                    date
                ")
                ->whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->where('activity_id', $kpi_item->activity_id)
                ->first();
           
            if($query) {
                $item['fact'] = $query->fact ?? 0;
                $item['avg'] = $query->avg ?? 0;
                $item['records_count'] = $query->records_count ?? 0;
            }
            
        }
    }

    /**
     * take cell value from analytics
     * for kpi item
     * 
     * @param $kpi_item
     * @param Carbon $date
     * @param float &$fact
     * 
     * @return float
     */
    private function takeCellValue(KpiItem $kpi_item, Carbon $date, float &$fact) : void
    {
        if($kpi_item->activity 
        && $kpi_item->activity->view == Activity::VIEW_CELL) {
            $fact = AnalyticStat::getCellValue(
                $kpi_item->activity->group_id,
                $kpi_item->cell,
                $date->format('Y-m-d')
            );
        }
    }

    /**
     * take rentability value from analytics
     * for kpi item
     * 
     * @param KpiItem $kpi_item
     * @param Carbon $date
     * @param float &$fact
     * 
     * @return float
     */
    private function takeRentability(KpiItem $kpi_item, Carbon $date, float &$rent) : void
    {
        if($kpi_item->activity
        && $kpi_item->activity->view == Activity::VIEW_RENTAB) {
            $rent = AnalyticStat::getRentability(
                $kpi_item->activity->group_id, 
                $date->format('Y-m-d')
            );
        }
    }

    /**
     * take cell value from analytics
     * for kpi item
     * 
     * @param $kpi_item
     * @param Carbon $date
     * @param float &$fact
     * @param int $user_id
     * 
     * @return float
     */
    private function takeUpdatedValue(
        KpiItem $kpi_item,
        Carbon $date,
        float &$fact,
        int $user_id
    ) : void
    {
        $has = $this->updatedValues
                ->where('user_id', $user_id)
                ->where('kpi_item_id', $kpi_item->id);

        if($kpi_item->activity_id != 0) $has = $has->where('activity_id', $kpi_item->activity_id);

        $has = $has->first();

        if($has) $fact = (float) $has->value;
    }
   

    /**
     * get users with user stats
     */
    private function getUserStats(Kpi $kpi, array $user_ids, Carbon $date) : \Illuminate\Support\Collection
    {
        $activities = $kpi->items
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
            ->leftJoinSub($sum_and_counts, 'sum_and_counts', function ($join)
			{
		     	$join->on('users.id', '=', 'sum_and_counts.user_id');
			})
			->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
			->where('ud.is_trainee', 0)
			->whereIn('users.id', $user_ids)
			->orderBy('last_name')
			->get();

          //  if(count($user_ids) == 1) dd($users);
        // group collection
		$users = $users->groupBy('id')
			->map(function($items) {
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

		return $users->values(); //array_values($users->toArray());
    }

    /**
     * @param Request $request
     * @return array
     */
    public function userWorkdays(Request $request): array
    {
        $default_date = ['year' => Carbon::now()->year, 'month' => Carbon::now()->month];
        $filters = $request->input('filters') ?? ['data_from' => $default_date];
        if(!array_key_exists('data_from', $filters)) $filters['data_from'] = $default_date;

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

            $ignore          = $user->working_day_id == 1 ? [6,0] : [0];
            $userWorkDays    = $this->workdays($userAppliedDate->year, $userAppliedDate->month, $userAppliedDate->day, $ignore);
            $workdaysInMonth = workdays($filters['data_from']['year'], $filters['data_from']['month'], $ignore);

            if ($userAppliedDate->year == $filters['data_from']['year'] && $userAppliedDate->month == $filters['data_from']['month'])
            {
                $result[] = [
                    'user_id'           => $user->user_id,
                    'activity_id'       => $user->activity_id,
                    'applied_at'        => $user->applied,
                    'user_work_days'    => $userWorkDays,
                    'workdays_in_month' => $workdaysInMonth,
                    'user_plan'         => $user->full_time == 1 ? $userWorkDays * $user->plan : $userWorkDays * $user->plan / 2,
                    'workdays'          => $user->working_day_id == 1 ? 5 : 6,
                    'weekdays'          => $user->weekdays
                ];
            }else{
                $result[] = [
                    'user_id'           => $user->user_id,
                    'activity_id'       => $user->activity_id,
                    'applied_at'        => $user->applied,
                    'user_work_days'    => $workdaysInMonth,
                    'workdays_in_month' => $workdaysInMonth,
                    'user_plan'         => $user->full_time == 1 ? $workdaysInMonth * $user->plan : $workdaysInMonth * $user->plan / 2,
                    'workdays'          => $user->working_day_id == 1 ? 5 : 6,
                    'weekdays'          => $user->weekdays
                ];
            }
        }

        return $result;
    }

    /**
     * @param $filters
     * @return Collection|array
     */
    private function getUserProfileGroup($filters): Collection|array
    {
        return User::query()
            ->join('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->join('group_user as gu', 'gu.user_id', '=', 'users.id')
            ->join('kpis as kp', fn ($kp) => $kp->on('kp.targetable_id', '=', 'gu.group_id')->where('kp.targetable_type', '=', self::PROFILE_GROUP))
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
    private function workdays($year, $month, $day, array $ignore = [6,0]): int
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
}