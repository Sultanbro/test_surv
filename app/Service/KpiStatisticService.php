<?php

namespace App\Service;

use App\Http\Requests\BonusesFilterRequest;
use App\Models\Kpi\Bonus;
use App\Position;
use App\Traits\KpiHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use App\User;
use App\ProfileGroup;
use App\Models\Kpi\KpiItem;
use App\Models\Kpi\Kpi;
use App\Models\Analytics\UserStat;
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
    public function fetchBonuses(BonusesFilterRequest $request) : array
    {
        $bonuses   = $this->getBonuses($request);

        $bonusesArray = [];
        foreach ($bonuses as $bonus)
        {
            $bonusesArray[] = $this->getTargetAbleData($bonus, $request);
        }

        return  $bonusesArray;

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
        ]))->get();
    }

    /**
     * Получаем данные по targetable_type, targetable_id
     * @param $bonus
     * @param $request
     * @return array
     */
    private function getTargetAbleData($bonus, $request): array
    {
        $month  = $request->month ?? null;
        $year   = $request->year ?? null;
        $userId = $request->user_id ?? null;

        $bonuses = [];

        $model = $bonus->targetable_type::query()
            ->when(in_array($bonus->targetable_type, [self::POSITION, self::PROFILE_GROUP]), fn ($group) => $group->with([
                    'users' => fn ($bonus) => $bonus->with(['obtainedBonuses.bonus' => fn($bonus) => $bonus->when($year && $month,
                        fn($bonus) => $bonus->whereYear('created_at', $year)->whereMonth('created_at', $month))])
                        ->when(isset($userId), fn($user) => $user->where('id', $userId))
                ])
            )->when($bonus->targetable_type == self::USER, fn ($user) =>
                    $user->with(['obtainedBonuses.bonus' => fn($bonus) => $bonus->when($year && $month,
                            fn($bonus) => $bonus->whereYear('created_at', $year)->whereMonth('created_at', $month))])
            )->where('id', $bonus->targetable_id)->first();

        $model->targetable_type = $bonus->targetable_type;
        $model->targetable_id   = $bonus->targetable_id;

        $bonuses[] = $model;

        return $bonuses;
    }
    /**
     * Список Квартальных премии
     */
    public function fetchQuartalPremiums(Request $request) : array
    {
        return [];
    }

    /**
     * Вытащить список kpi со статистикой
     * 
     * getUsersForKpi($kpi)
     * getUserStats($kpi, $_user_ids, $date)
     * connectKpiWithUserStats(Kpi $kpi, $_users)
     */
    public function fetchKpis(array $filters) : array
    {
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
     * helper for fetchKpis()
     */
    private function getUsersForKpi(Kpi $kpi, $date, $user_id = 0)
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
        $users = $this->connectKpiWithUserStats($kpi, $_users, $date);

        return $users;
    }

    /**
     * create final users array
     */
    private function connectKpiWithUserStats(Kpi $kpi, $_users, $date) {

        // count workdays in month
        $workdays = [];
        $workdays[5] = workdays($date->year, $date->month, [6,0]);
        $workdays[6] = workdays($date->year, $date->month, [0]);

        // fill users array
        $users = [];
        
        $cell_activities = Activity::withTrashed()->where('view', Activity::VIEW_CELL)->get();

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
                    $item['fact'] = $exists->fact;
                    $item['avg'] = $exists->avg;
                    $item['records_count'] = $exists->records_count;
                    $item['applied'] = $exists->applied;
                    $item['days'] = $exists->days;
                    $item['registered'] = $exists->registered_days;
                } else {
                    $item['fact'] = 0;
                    $item['avg'] = 0;
                    $item['records_count'] = 0;
                    $item['applied'] = null;
                    $item['days'] = 0;
                    $item['registered'] = 0;
                }   
                
                // // take cell value
                $hasCellActivity = $cell_activities->where('id', $item['activity_id'])->first();
                if($hasCellActivity) {
                    $item['fact'] = AnalyticStat::getCellValue(
                        $hasCellActivity->group_id,
                        $hasCellActivity->cell,
                        $date->format('Y-m-d')
                    );
                }
               
                // plan
                $item['plan'] = $_item->activity ? $_item->activity->daily_plan : 0;
                $item['workdays'] = $_item->activity && $_item->activity->workdays != 0 ?  $workdays[(int) $_item->activity->workdays] : $workdays[5];
                $item['full_time'] = $user['full_time'];


                $kpi_items[] = $item;
            }
            
            $user['items'] = $kpi_items;
            
        
            $users[] = $user;
        }

        return $users;
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

}